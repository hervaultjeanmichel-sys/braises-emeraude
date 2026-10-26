<?php

declare(strict_types=1);

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use DOMXPath;

/**
 * Nettoie le HTML importé de WordPress (Divi, WPBakery, Gutenberg) :
 * ne garde que le balisage sémantique, retire classes / styles / wrappers,
 * réécrit les liens internes en relatif et délègue le rapatriement des images.
 */
final class HtmlCleaner
{
    private const KEEP = ['h2', 'h3', 'h4', 'p', 'ul', 'ol', 'li', 'strong', 'em', 'a', 'img', 'br', 'blockquote', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'figure', 'figcaption', 'iframe'];

    private const RENAME = ['h1' => 'h2', 'h5' => 'h4', 'h6' => 'h4', 'b' => 'strong', 'i' => 'em'];

    private const DROP = ['script', 'style', 'noscript', 'form', 'input', 'button', 'svg', 'link', 'meta'];

    private const ATTRS = ['a' => ['href'], 'img' => ['src', 'alt', 'width', 'height'], 'iframe' => ['src', 'width', 'height', 'allowfullscreen', 'title'], 'td' => ['colspan', 'rowspan'], 'th' => ['colspan', 'rowspan', 'scope']];

    private const INLINE = ['strong', 'em', 'a', 'br', 'img'];

    /** @var callable(string): ?string  url d'image distante → url locale (ou null pour garder) */
    private $imageResolver;

    public array $stats = ['images' => 0, 'images_ok' => 0, 'links' => 0];

    public function __construct(private string $oldHost = 'braisesemeraude.com', ?callable $imageResolver = null)
    {
        $this->imageResolver = $imageResolver ?? fn (string $u) => null;
    }

    public function clean(string $html, ?string $title = null): string
    {
        // shortcodes et commentaires
        $html = preg_replace(['/\[\/?(?:et_pb|vc_|fusion|av_)[^\]]*\]/i', '/<!--.*?-->/s'], '', $html);

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><html><body><div id="__root">'.$html.'</div></body></html>', LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);
        libxml_clear_errors();
        $root = $doc->getElementById('__root');
        if (! $root) {
            return trim($html);
        }

        $this->walk($root);
        $this->wrapLooseInline($root);
        $this->removeEmpty($root);
        $this->dropLeadingTitle($root, $title);

        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $part = trim($doc->saveHTML($child));
            if ($part !== '') {
                $out .= $part."\n";
            }
        }

        return trim($out);
    }

    private function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText) {
                $txt = preg_replace('/[ \t\r\n\x{00A0}]+/u', ' ', $child->nodeValue);
                // « confort.Nos » → « confort. Nos »
                $child->nodeValue = preg_replace('/([a-zà-ÿ0-9])([.!?:;])([A-ZÀ-Ý])/u', '$1$2 $3', $txt);

                continue;
            }
            if (! $child instanceof DOMElement) {
                $node->removeChild($child);

                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);

                continue;
            }
            if (isset(self::RENAME[$tag])) {
                $child = $this->rename($child, self::RENAME[$tag]);
                $tag = $child->tagName;
            }
            $this->walk($child);
            if (! in_array($tag, self::KEEP, true)) {
                $this->unwrap($child);

                continue;
            }
            $this->filterAttributes($child);
            if ($tag === 'iframe' && ! preg_match('#(youtube|youtube-nocookie|vimeo)\.com#', $child->getAttribute('src'))) {
                $node->removeChild($child);
            }
        }
        // <p> qui contient des blocs (ex. « <p><div> » de Divi) : on le défait
        if ($node instanceof DOMElement && $node->tagName === 'p') {
            foreach ($node->childNodes as $c) {
                if ($c instanceof DOMElement && in_array($c->tagName, ['p', 'h2', 'h3', 'h4', 'ul', 'ol', 'table', 'blockquote', 'figure'], true)) {
                    $this->unwrap($node);
                    break;
                }
            }
        }
    }

    private function filterAttributes(DOMElement $el): void
    {
        $keep = self::ATTRS[$el->tagName] ?? [];
        foreach (iterator_to_array($el->attributes) as $attr) {
            if (! in_array($attr->name, $keep, true)) {
                $el->removeAttribute($attr->name);
            }
        }
        if ($el->tagName === 'a') {
            $href = $el->getAttribute('href');
            $rel = preg_replace('#^https?://(www\.)?'.preg_quote($this->oldHost, '#').'#i', '', $href);
            if ($rel !== $href) {
                $el->setAttribute('href', $rel === '' ? '/' : $rel);
                $this->stats['links']++;
            }
            if (! str_starts_with($el->getAttribute('href'), '/') && ! str_starts_with($el->getAttribute('href'), '#') && ! str_starts_with($el->getAttribute('href'), 'tel:') && ! str_starts_with($el->getAttribute('href'), 'mailto:')) {
                $el->setAttribute('rel', 'noopener');
            }
        }
        if ($el->tagName === 'img') {
            $src = $el->getAttribute('src');
            if (preg_match('#^https?://(www\.)?'.preg_quote($this->oldHost, '#').'/#i', $src)) {
                $this->stats['images']++;
                if ($local = ($this->imageResolver)($src)) {
                    $el->setAttribute('src', $local);
                    $this->stats['images_ok']++;
                }
            }
            $el->setAttribute('loading', 'lazy');
            $el->setAttribute('decoding', 'async');
        }
    }

    private function rename(DOMElement $el, string $tag): DOMElement
    {
        $new = $el->ownerDocument->createElement($tag);
        while ($el->firstChild) {
            $new->appendChild($el->firstChild);
        }
        $el->parentNode->replaceChild($new, $el);

        return $new;
    }

    private function unwrap(DOMElement $el): void
    {
        $parent = $el->parentNode;
        $isBlock = ! in_array($el->tagName, ['span', 'font', 'u', 'small', 'big', 'sup', 'sub', 'label'], true);
        if ($isBlock) {
            $parent->insertBefore($el->ownerDocument->createTextNode(' '), $el);
        }
        while ($el->firstChild) {
            $parent->insertBefore($el->firstChild, $el);
        }
        $parent->removeChild($el);
    }

    /** Regroupe le texte et les éléments en ligne laissés au premier niveau dans des <p>. */
    private function wrapLooseInline(DOMElement $root): void
    {
        $doc = $root->ownerDocument;
        $buffer = [];
        $flush = function (?DOMNode $before) use (&$buffer, $doc, $root) {
            $text = trim(implode('', array_map(fn ($n) => $n->textContent, $buffer)));
            $hasImg = (bool) array_filter($buffer, fn ($n) => $n instanceof DOMElement && ($n->tagName === 'img' || $n->getElementsByTagName('img')->length));
            if ($text !== '' || $hasImg) {
                $p = $doc->createElement('p');
                $root->insertBefore($p, $before);
                foreach ($buffer as $n) {
                    $p->appendChild($n);
                }
            } else {
                foreach ($buffer as $n) {
                    $root->removeChild($n);
                }
            }
            $buffer = [];
        };
        foreach (iterator_to_array($root->childNodes) as $child) {
            if ($child instanceof DOMText || ($child instanceof DOMElement && in_array($child->tagName, self::INLINE, true))) {
                $buffer[] = $child;
            } else {
                $flush($child);
            }
        }
        $flush(null);
    }

    private function removeEmpty(DOMElement $root): void
    {
        $xp = new DOMXPath($root->ownerDocument);
        do {
            $removed = 0;
            foreach ($xp->query('.//p|.//strong|.//em|.//a|.//h2|.//h3|.//h4|.//li|.//ul|.//ol|.//blockquote|.//figure', $root) as $el) {
                if (trim(str_replace("\u{00A0}", ' ', $el->textContent)) === '' && ! $xp->query('.//img|.//iframe', $el)->length) {
                    $el->parentNode->removeChild($el);
                    $removed++;
                }
            }
        } while ($removed);
        // <br> en début / fin de paragraphe et en rafale
        foreach ($xp->query('.//p', $root) as $p) {
            while ($p->firstChild && ($p->firstChild instanceof DOMElement && $p->firstChild->tagName === 'br' || ($p->firstChild instanceof DOMText && trim($p->firstChild->nodeValue) === ''))) {
                $p->removeChild($p->firstChild);
            }
            while ($p->lastChild && ($p->lastChild instanceof DOMElement && $p->lastChild->tagName === 'br' || ($p->lastChild instanceof DOMText && trim($p->lastChild->nodeValue) === ''))) {
                $p->removeChild($p->lastChild);
            }
        }
    }

    /** Retire un titre initial qui répète le H1 de la page. */
    private function dropLeadingTitle(DOMElement $root, ?string $title): void
    {
        if (! $title) {
            return;
        }
        $seen = 0;
        foreach (iterator_to_array($root->childNodes) as $child) {
            if (! $child instanceof DOMElement || ++$seen > 3) {
                continue;
            }
            if (in_array($child->tagName, ['h2', 'h3'], true)) {
                if (mb_strtolower(trim($child->textContent)) === mb_strtolower(trim($title))) {
                    $root->removeChild($child);
                }

                return;
            }
        }
    }
}

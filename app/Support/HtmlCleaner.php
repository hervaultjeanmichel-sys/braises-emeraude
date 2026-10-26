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

    public array $stats = ['images' => 0, 'images_ok' => 0, 'links' => 0, 'pdf' => 0];

    /** Caractéristiques extraites du tableau technique WooCommerce : [{label, value}] */
    public array $specs = [];

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
        $this->extractSpecs($root);
        $this->dropBoilerplate($root);
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
            if (preg_match('#\.(png|jpe?g|gif|webp)(\?.*)?$#i', $href)) {
                $el->removeAttribute('href');
                $el->setAttribute('data-unwrap', '1');

                return;
            }
            if (preg_match('#^https?://(www\.)?'.preg_quote($this->oldHost, '#').'/wp-content/.+\.pdf$#i', $href)) {
                $this->stats['pdf']++;
                if ($local = ($this->imageResolver)($href)) {
                    $el->setAttribute('href', $local);
                    $href = $local;
                }
            }
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
        $isBlock = ! in_array($el->tagName, ['span', 'font', 'u', 'small', 'big', 'sup', 'sub', 'label', 'strong', 'em', 'a', 'b', 'i'], true);
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

    /** Tableau « picto | libellé | valeur » → $this->specs, puis retrait du tableau. */
    private function extractSpecs(DOMElement $root): void
    {
        $xp = new DOMXPath($root->ownerDocument);
        foreach (iterator_to_array($xp->query('.//table', $root)) as $table) {
            $specs = [];
            foreach ($xp->query('.//tr', $table) as $tr) {
                $cells = [];
                foreach ($tr->childNodes as $c) {
                    if ($c instanceof DOMElement && in_array($c->tagName, ['th', 'td'], true) && trim($c->textContent) !== '') {
                        $cells[] = trim(preg_replace('/\s+/u', ' ', $c->textContent));
                    }
                }
                if (count($cells) === 2) {
                    $label = rtrim($cells[0], " :\u{00A0}");
                    $value = $cells[1];
                    if (! preg_match('/^NC\b/i', $value)) {
                        $specs[] = ['label' => $label, 'value' => $value];
                    }
                }
            }
            if (count($specs) >= 3) {
                $this->specs = array_merge($this->specs, $specs);
                $table->parentNode->removeChild($table);
            }
        }
        // liens d'images démontés
        foreach (iterator_to_array($xp->query('.//a[@data-unwrap]', $root)) as $a) {
            $this->unwrap($a);
        }
        // titres : pas de gras/italique à l'intérieur
        foreach (iterator_to_array($xp->query('.//h2/strong|.//h3/strong|.//h4/strong|.//h2/em|.//h3/em', $root)) as $el) {
            $this->unwrap($el);
        }
    }

    /** Retire les blocs répétés de WordPress : grilles de produits WooCommerce, encart « L'expertise Braises Émeraude ». */
    private function dropBoilerplate(DOMElement $root): void
    {
        $xp = new DOMXPath($root->ownerDocument);
        foreach (iterator_to_array($xp->query('.//ul', $root)) as $ul) {
            $lis = $xp->query('./li', $ul);
            $cards = $xp->query('./li[.//a[starts-with(@href, "/produit/")] and .//img]', $ul);
            if ($lis->length > 0 && $cards->length === $lis->length) {
                $ul->parentNode->removeChild($ul);
            }
        }
        foreach (iterator_to_array($xp->query('.//h2|.//h3', $root)) as $h) {
            if ($h->parentNode && preg_match('/expertise braises? [ée]meraude/iu', $h->textContent)) {
                $next = $h->nextSibling;
                $h->parentNode->removeChild($h);
                while ($next && ! ($next instanceof DOMElement && in_array($next->tagName, ['h2', 'h3'], true))) {
                    $after = $next->nextSibling;
                    $next->parentNode->removeChild($next);
                    $next = $after;
                }
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

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AidTopic;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aides 2026 : réécrit /aides-financement/, crée les fiches /aides/{slug}/ et corrige
 * les anciens textes qui promettent MaPrimeRénov’ ou le crédit d’impôt pour un poêle.
 *
 *   php artisan braises:fix-aides            → simulation : affiche avant / après, n’écrit rien
 *   php artisan braises:fix-aides --apply    → écrit en base
 */
class FixAides extends Command
{
    protected $signature = 'braises:fix-aides {--apply : écrit les modifications}';

    protected $description = 'Met à jour les aides 2026 (fin de MaPrimeRénov’ par geste pour les poêles)';

    private const PATTERN = '/ma ?prime ?r[ée]nov|cr[ée]dit d.imp[ôo]t/iu';

    private const TABLES = ['pages' => 'slug', 'posts' => 'slug', 'cities' => 'slug', 'stores' => 'slug', 'products' => 'slug', 'brands' => 'slug', 'product_categories' => 'path'];

    private array $data;

    private string $marker = "\u{241E}R\u{241E}";

    public function handle(): int
    {
        $this->data = require database_path('data/content-aides.php');
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'MODE ÉCRITURE' : 'SIMULATION (rien n’est écrit) — relancer avec --apply');

        $this->page($apply);
        $this->topics($apply);
        $changed = $this->fixOldTexts($apply);

        if ($apply) {
            Cache::flush();
        }
        $this->newLine();
        $this->info("Terminé : page aides + ".count($this->data['topics'])." fiches + {$changed} ancien(s) texte(s) corrigé(s)".($apply ? '' : ' (simulation)'));

        return self::SUCCESS;
    }

    private function page(bool $apply): void
    {
        $page = Page::where('slug', 'aides-financement')->first();
        if (! $page) {
            $this->error('Page aides-financement introuvable');

            return;
        }
        $this->line('— page /aides-financement/ : réécrite entièrement ('.implode(', ', array_keys($this->data['page'])).')');
        if ($apply) {
            $page->forceFill($this->data['page'])->save();
        }
    }

    private function topics(bool $apply): void
    {
        foreach ($this->data['topics'] as $t) {
            $exists = AidTopic::where('slug', $t['slug'])->exists();
            $this->line('— fiche /aides/'.$t['slug'].'/ : '.($exists ? 'mise à jour' : 'créée'));
            if ($apply) {
                AidTopic::updateOrCreate(['slug' => $t['slug']], $t + ['is_published' => true]);
            }
        }
    }

    private function fixOldTexts(bool $apply): int
    {
        $count = 0;
        foreach (self::TABLES as $table => $key) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $cols = array_intersect(Schema::getColumnListing($table), ['title', 'h1', 'intro', 'excerpt', 'content', 'sections', 'faq', 'seo_title', 'seo_description']);
            foreach (DB::table($table)->get() as $row) {
                if ($table === 'pages' && $row->slug === 'aides-financement') {
                    continue;
                }
                $update = [];
                foreach ($cols as $col) {
                    $raw = $row->{$col};
                    if (! is_string($raw) || $raw === '') {
                        continue;
                    }
                    $json = json_decode($raw, true);
                    $haystack = is_array($json) ? json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $raw;
                    if (! preg_match(self::PATTERN, $this->decode($haystack))) {
                        continue;
                    }
                    $new = is_array($json)
                        ? json_encode($this->walk($json), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                        : $this->fix($raw);
                    if ($new !== $raw) {
                        $update[$col] = $new;
                        $this->newLine();
                        $this->comment("===== {$table} / {$row->{$key}} / {$col}");
                        foreach ($this->snippets($haystack) as $s) {
                            $this->line('  <fg=red>AVANT</> '.$s);
                        }
                        foreach ($this->snippets($new, true) as $s) {
                            $this->line('  <fg=green>APRÈS</> '.$s);
                        }
                    }
                }
                if ($update) {
                    $count++;
                    if ($apply) {
                        DB::table($table)->where('id', $row->id)->update($update + ['updated_at' => now()]);
                    }
                }
            }
        }

        return $count;
    }

    private function walk(array $a): array
    {
        foreach ($a as $k => $v) {
            $a[$k] = is_array($v) ? $this->walk($v) : (is_string($v) ? $this->fix($v) : $v);
        }

        return $a;
    }

    /** Corrige un texte : règles appliquées hors phrase de remplacement déjà posée, une seule occurrence gardée. */
    private function fix(string $text): string
    {
        $text = $this->decode($text);
        if (! preg_match(self::PATTERN, $text)) {
            return $text;
        }
        $isHtml = $text !== strip_tags($text);
        $replacement = $isHtml ? $this->data['replacement'] : strip_tags($this->data['replacement']);

        $parts = explode($replacement, $text);
        foreach ($parts as $i => $part) {
            foreach ($this->data['rules'] as [$re, $to]) {
                $part = preg_replace($re, str_replace('«R»', $this->marker, $to), $part);
            }
            $parts[$i] = $part;
        }
        $text = implode($this->marker, $parts);

        // une seule phrase de remplacement par texte ; on nettoie les blocs vidés
        $first = strpos($text, $this->marker);
        if ($first !== false) {
            $text = substr($text, 0, $first + strlen($this->marker)).str_replace($this->marker, '', substr($text, $first + strlen($this->marker)));
        }
        $text = preg_replace('/([.!?])'.preg_quote($this->marker, '/').'/u', '$1 '.$this->marker, $text);
        $text = str_replace($this->marker, $replacement, $text);
        $text = preg_replace(['/<(p|li|strong|em)>\s*<\/\1>/u', '/[ \t]{2,}/u'], ['', ' '], $text);

        return trim($text) === '' ? strip_tags($replacement) : $text;
    }

    /** Décode les entités typographiques (’ «  ») sans toucher à &lt; &gt; &amp;. */
    private function decode(string $s): string
    {
        return preg_replace_callback('/&(?!lt;|gt;|amp;|quot;)#?[a-z0-9]+;/i', fn ($m) => html_entity_decode($m[0], ENT_QUOTES | ENT_HTML5, 'UTF-8'), $s);
    }

    private function snippets(string $s, bool $after = false): array
    {
        $plain = preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(str_replace(['\\n', '\\t'], ' ', $this->decode($s))), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $needle = $after ? '/septembre 2026|Les aides en vigueur|prime CEE/u' : self::PATTERN;
        preg_match_all('/.{0,90}(?:'.trim($needle, '/iu').').{0,120}/iu', $plain, $m);

        return array_slice(array_unique($m[0]), 0, 6);
    }
}

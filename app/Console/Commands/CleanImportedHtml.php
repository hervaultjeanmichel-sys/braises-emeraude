<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\HtmlCleaner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Nettoie le HTML importé de WordPress (Divi / WPBakery) et rapatrie ses images en WebP.
 *
 *   php artisan braises:clean-html                     → simulation (rien n'est écrit ni téléchargé)
 *   php artisan braises:clean-html --show=chauffage    → simulation + aperçu du résultat pour un slug
 *   php artisan braises:clean-html --apply             → écrit en base et télécharge les images
 */
class CleanImportedHtml extends Command
{
    protected $signature = 'braises:clean-html {--apply} {--show= : slug dont on affiche le HTML nettoyé}';

    protected $description = 'Nettoie le HTML WordPress importé (Divi, WPBakery) et rapatrie les images';

    private const TABLES = ['pages' => 'slug', 'posts' => 'slug', 'cities' => 'slug', 'stores' => 'slug', 'products' => 'slug', 'brands' => 'slug', 'product_categories' => 'path'];

    private const MARKERS = '/et_pb_|vc_|wpb_|wp-content|class=|style=|<div|<span/i';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'MODE ÉCRITURE' : 'SIMULATION (rien n’est écrit) — relancer avec --apply');

        $rows = [];
        $tot = ['records' => 0, 'before' => 0, 'after' => 0, 'images' => 0, 'images_ok' => 0, 'links' => 0, 'pdf' => 0, 'specs' => 0];

        foreach (self::TABLES as $table => $key) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'content')) {
                continue;
            }
            $titleCol = collect(['h1', 'title', 'name'])->first(fn ($c) => Schema::hasColumn($table, $c));
            foreach (DB::table($table)->whereNotNull('content')->get() as $row) {
                $html = (string) $row->content;
                if (! preg_match(self::MARKERS, $html)) {
                    continue;
                }
                $cleaner = new HtmlCleaner('braisesemeraude.com', fn (string $url) => $apply ? $this->image($url) : (preg_match('/\.pdf$/i', $url) ? '/storage/wp/pdf/…' : '/storage/wp/'.$this->imageName($url)));
                $new = $cleaner->clean($html, $titleCol ? $row->{$titleCol} : null);

                $tot['records']++;
                $tot['before'] += strlen($html);
                $tot['after'] += strlen($new);
                foreach (['images', 'images_ok', 'links', 'pdf'] as $k) {
                    $tot[$k] += $cleaner->stats[$k];
                }
                $rows[] = [$table, $row->{$key}, number_format(strlen($html), 0, ',', ' '), number_format(strlen($new), 0, ',', ' '), $cleaner->stats['images'].($apply ? ' ('.$cleaner->stats['images_ok'].' ok)' : ''), $cleaner->stats['links'], count($cleaner->specs)];

                if ($this->option('show') === $row->{$key}) {
                    $this->newLine();
                    $this->comment("===== Aperçu {$table} / {$row->{$key}}");
                    $this->line($new);
                }
                $update = $new !== $html ? ['content' => $new] : [];
                // fiches produits : tableau technique → champs specs / power_kw (s'ils sont vides)
                if ($table === 'products' && $cleaner->specs) {
                    $tot['specs']++;
                    if (blank($row->specs) || $row->specs === '[]') {
                        $update['specs'] = json_encode($cleaner->specs, JSON_UNESCAPED_UNICODE);
                    }
                    if (blank($row->power_kw)) {
                        foreach ($cleaner->specs as $sp) {
                            if (preg_match('/puissance/i', $sp['label']) && preg_match('/(\d+(?:[.,]\d+)?)\s*kW/i', $sp['value'], $m)) {
                                $update['power_kw'] = (float) str_replace(',', '.', $m[1]);
                                break;
                            }
                        }
                    }
                }
                if ($apply && $update) {
                    DB::table($table)->where('id', $row->id)->update($update + ['updated_at' => now()]);
                }
            }
        }

        $this->table(['Table', 'Fiche', 'Avant (car.)', 'Après (car.)', 'Images', 'Liens relatifs', 'Caract.'], $rows);
        $this->info(sprintf('Terminé : %d fiche(s), %s → %s caractères, %d image(s)%s, %d PDF, %d lien(s) rendus relatifs, %d fiche(s) produit avec caractéristiques extraites%s',
            $tot['records'], number_format($tot['before'], 0, ',', ' '), number_format($tot['after'], 0, ',', ' '),
            $tot['images'], $apply ? " dont {$tot['images_ok']} rapatriée(s)" : '', $tot['pdf'], $tot['links'], $tot['specs'], $apply ? '' : ' (simulation)'));

        if ($apply) {
            Cache::flush();
        }

        return self::SUCCESS;
    }

    private function imageName(string $url): string
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        // retire le suffixe de taille WordPress (-480x300) pour reprendre l'original
        $base = preg_replace('/-\d+x\d+$/', '', pathinfo($path, PATHINFO_FILENAME));

        return Str::slug($base).'-'.substr(md5(dirname($path)), 0, 6).'.webp';
    }

    /** Télécharge l'image (version originale si possible), redimensionne à 1600 px max, WebP q80. */
    private function image(string $url): ?string
    {
        if (preg_match('/\.pdf$/i', $url)) {
            $name = 'wp/pdf/'.Str::slug(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME)).'.pdf';
            if (! Storage::disk('public')->exists($name)) {
                $res = Http::timeout(60)->get($url);
                if (! $res->ok() || ! str_starts_with($res->body(), '%PDF')) {
                    $this->warn("  PDF non rapatrié : {$url}");

                    return null;
                }
                Storage::disk('public')->put($name, $res->body());
            }

            return '/storage/'.$name;
        }
        $name = 'wp/'.$this->imageName($url);
        if (Storage::disk('public')->exists($name)) {
            return '/storage/'.$name;
        }
        $original = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $url);
        foreach (array_unique([$original, $url]) as $try) {
            $res = Http::timeout(60)->get($try);
            if ($res->ok() && ($img = @imagecreatefromstring($res->body()))) {
                imagepalettetotruecolor($img);
                imagesavealpha($img, true);
                if (imagesx($img) > 1600) {
                    $img = imagescale($img, 1600);
                }
                ob_start();
                imagewebp($img, null, 80);
                Storage::disk('public')->put($name, (string) ob_get_clean());
                imagedestroy($img);

                return '/storage/'.$name;
            }
        }
        $this->warn("  image non rapatriée : {$url}");

        return null;
    }
}

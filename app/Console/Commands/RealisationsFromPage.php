<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Realisation;
use Illuminate\Console\Command;

/**
 * Crée des réalisations en BROUILLON à partir des photos de l'ancienne page /nos-realisations/.
 * À compléter ensuite dans l'admin (commune, magasin, appareil, avis avec accord du client).
 *
 *   php artisan braises:realisations-from-page            → simulation
 *   php artisan braises:realisations-from-page --apply
 */
class RealisationsFromPage extends Command
{
    protected $signature = 'braises:realisations-from-page {--apply} {--min=300 : largeur minimale (px) pour ignorer les pictos}';

    protected $description = 'Photos de l’ancienne page Réalisations → réalisations en brouillon';

    public function handle(): int
    {
        $page = Page::where('slug', 'nos-realisations')->first();
        if (! $page || ! $page->content) {
            $this->error('Page nos-realisations introuvable ou vide');

            return self::FAILURE;
        }
        preg_match_all('/<img\b[^>]*>/i', $page->content, $tags);
        $existing = Realisation::pluck('photos')->flatten()->filter()->all();
        $n = 0;
        foreach ($tags[0] as $tag) {
            preg_match('/src="([^"]+)"/i', $tag, $src);
            preg_match('/width="(\d+)"/i', $tag, $w);
            preg_match('/alt="([^"]*)"/i', $tag, $alt);
            $url = $src[1] ?? '';
            if (! str_starts_with($url, '/storage/') || (isset($w[1]) && (int) $w[1] < (int) $this->option('min'))) {
                continue;
            }
            $path = substr($url, strlen('/storage/'));
            if (in_array($path, $existing, true)) {
                continue;
            }
            $existing[] = $path;
            $n++;
            $title = trim(html_entity_decode($alt[1] ?? ''));
            $title = $title !== '' && mb_strlen($title) < 90 ? $title : 'Réalisation n° '.$n.' (à compléter)';
            $this->line("  + {$title}  ←  {$url}");
            if ($this->option('apply')) {
                Realisation::create(['title' => $title, 'photos' => [$path], 'is_published' => false, 'sort' => $n]);
            }
        }
        $this->info("{$n} réalisation(s) en brouillon".($this->option('apply') ? ' créée(s)' : ' (simulation)').' — à compléter dans Admin › Réalisations');

        return self::SUCCESS;
    }
}

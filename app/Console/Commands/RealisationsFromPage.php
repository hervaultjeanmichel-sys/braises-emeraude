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
    protected $signature = 'braises:realisations-from-page {--apply} {--publish-titled : publie celles qui ont une vraie légende} {--min=300 : largeur minimale (px) pour ignorer les pictos}';

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
            $legend = trim(html_entity_decode($alt[1] ?? '', ENT_QUOTES | ENT_HTML5));
            $titled = $legend !== '' && mb_strlen($legend) < 120;
            $title = $titled ? rtrim(mb_strtoupper(mb_substr($legend, 0, 1)).mb_substr($legend, 1), ' .!') : 'Réalisation n° '.$n.' (à compléter)';
            $city = $this->city($legend);
            $energy = $this->energy($legend);
            $publish = $titled && $this->option('publish-titled');
            $this->line(sprintf('  + %s%s%s%s', $title, $city ? "  [commune : {$city}]" : '', $energy ? "  [énergie : {$energy}]" : '', $publish ? '  [publiée]' : '  [brouillon]'));
            if ($this->option('apply')) {
                Realisation::create(['title' => $title, 'city' => $city, 'energy' => $energy, 'photos' => [$path], 'is_published' => $publish, 'sort' => $n]);
            }
        }
        $this->info("{$n} réalisation(s)".($this->option('apply') ? ' créée(s)' : ' (simulation)').' — à compléter dans Admin › Réalisations');

        return self::SUCCESS;
    }

    /** Commune citée dans la légende : « … à Lancieux », « … à Mesnil-Roc'h », « région de Dinard ». */
    private function city(string $t): ?string
    {
        if (preg_match("/\\b(?:à|a|région de|sur)\\s+((?:Saint|Sainte|La|Le|Les|Mesnil|Pleurtuit|Dinard|Dinan|Lancieux|Cancale|Rennes|Dol|Combourg|Pleslin|Ploubalay|Beaussais|Pleudihen|Châteauneuf|Miniac|Plerguer|Tinténiac)[\\p{L}'’\\- ]{0,30}?)(?:\\s*\\(\\d{2}\\))?(?=[.,!;]|\\s*$)/u", $t, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function energy(string $t): ?string
    {
        $t = mb_strtolower($t);

        return match (true) {
            str_contains($t, 'mixte') || str_contains($t, 'duo') => 'mixte',
            str_contains($t, 'granul') || str_contains($t, 'pellet') => 'granules',
            str_contains($t, ' bois') || str_contains($t, 'foyer') || str_contains($t, 'insert 3 faces') => 'bois',
            default => null,
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Coordonnées GPS des magasins (pour la carte) :
 *  1. depuis le lien Google Business (suivi des redirections jusqu'à Google Maps) ;
 *  2. sinon depuis l'adresse, via OpenStreetMap (Nominatim).
 *
 *   php artisan braises:geocode-stores            → simulation
 *   php artisan braises:geocode-stores --apply    → enregistre latitude / longitude
 *   php artisan braises:geocode-stores --force    → recalcule même si déjà renseignées
 */
class GeocodeStores extends Command
{
    protected $signature = 'braises:geocode-stores {--apply} {--force}';

    protected $description = 'Renseigne latitude / longitude des magasins depuis Google Business ou l’adresse';

    private const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'MODE ÉCRITURE' : 'SIMULATION — relancer avec --apply');

        foreach (Store::orderBy('sort')->get() as $store) {
            $this->newLine();
            $this->comment("— {$store->name}");
            if ($store->latitude && $store->longitude && ! $this->option('force')) {
                $this->line("  déjà renseigné : {$store->latitude}, {$store->longitude}");

                continue;
            }

            [$coords, $how] = [null, ''];
            if ($store->google_business_url) {
                [$coords, $finalUrl] = $this->fromGoogle($store->google_business_url);
                $how = 'lien Google Business';
                if (! $coords) {
                    $this->warn('  pas de coordonnées dans le lien Google (URL finale : '.mb_strimwidth((string) $finalUrl, 0, 160, '…').')');
                }
            }
            if (! $coords && ($q = trim($store->addressLine()))) {
                $coords = $this->fromAddress($q);
                $how = "adresse « {$q} » (OpenStreetMap)";
            }
            if (! $coords) {
                $this->error('  introuvable : renseignez latitude / longitude dans l’admin');

                continue;
            }

            [$lat, $lng] = $coords;
            $this->line(sprintf('  <fg=green>%.6f, %.6f</> via %s', $lat, $lng, $how));
            $this->line(sprintf('  vérifier : https://www.openstreetmap.org/?mlat=%.6f&mlon=%.6f#map=17/%.6f/%.6f', $lat, $lng, $lat, $lng));
            if ($apply) {
                $store->forceFill(['latitude' => round($lat, 7), 'longitude' => round($lng, 7)])->save();
            }
        }
        if ($apply) {
            Cache::flush();
        }

        return self::SUCCESS;
    }

    /** @return array{0: ?array{0: float, 1: float}, 1: ?string} */
    private function fromGoogle(string $url): array
    {
        try {
            $res = Http::withHeaders(['User-Agent' => self::UA, 'Accept-Language' => 'fr-FR,fr;q=0.9'])
                ->withOptions(['allow_redirects' => ['max' => 10, 'track_redirects' => true]])
                ->timeout(20)->get($url);
        } catch (\Throwable $e) {
            $this->warn('  lien Google inaccessible : '.$e->getMessage());

            return [null, null];
        }
        $urls = [$url, ...$res->toPsrResponse()->getHeader('X-Guzzle-Redirect-History')];
        $finalUrl = (string) end($urls);
        $haystack = urldecode(implode(' ', $urls)).' '.substr($res->body(), 0, 400000);

        $patterns = [
            '/!3d(-?\d{1,2}\.\d{3,})!4d(-?\d{1,3}\.\d{3,})/',          // data=!3d48.61!4d-2.05 (le repère)
            '/@(-?\d{1,2}\.\d{3,}),(-?\d{1,3}\.\d{3,}),\d/',          // /@48.61,-2.05,17z
            '/[?&](?:q|ll|center|query)=(-?\d{1,2}\.\d{3,}),\s*(-?\d{1,3}\.\d{3,})/',
            '/APP_INITIALIZATION_STATE=\[\[\[[\d.]+,(-?\d{1,3}\.\d+),(-?\d{1,2}\.\d+)\]/', // [zoom, lng, lat]
        ];
        foreach ($patterns as $i => $re) {
            if (preg_match($re, $haystack, $m)) {
                [$a, $b] = [(float) $m[1], (float) $m[2]];
                $coords = $i === 3 ? [$b, $a] : [$a, $b];
                if ($this->inBrittany($coords)) {
                    return [$coords, $finalUrl];
                }
            }
        }

        return [null, $finalUrl];
    }

    /** @return ?array{0: float, 1: float} */
    private function fromAddress(string $q): ?array
    {
        try {
            $res = Http::withHeaders(['User-Agent' => 'BraisesEmeraude-site/1.0 (contact via braisesemeraude.com)'])
                ->timeout(20)->get('https://nominatim.openstreetmap.org/search', ['q' => $q.', France', 'format' => 'json', 'limit' => 1, 'countrycodes' => 'fr']);
            $hit = $res->json(0);
        } catch (\Throwable) {
            return null;
        }
        if (! $hit) {
            return null;
        }
        $coords = [(float) $hit['lat'], (float) $hit['lon']];

        return $this->inBrittany($coords) ? $coords : null;
    }

    /** Garde-fou : Ille-et-Vilaine et alentours. */
    private function inBrittany(array $c): bool
    {
        return $c[0] > 47.0 && $c[0] < 49.0 && $c[1] > -3.5 && $c[1] < -0.8;
    }
}

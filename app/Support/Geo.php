<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/** Géolocalisation d'adresses (OpenStreetMap / Nominatim, résultats en cache 30 jours) et distances. */
final class Geo
{
    /** @return ?array{0: float, 1: float} [lat, lng] */
    public static function locate(string $query): ?array
    {
        $query = trim($query);
        if ($query === '') {
            return null;
        }

        return Cache::remember('geo:'.md5(mb_strtolower($query)), now()->addDays(30), function () use ($query) {
            try {
                $hit = Http::withHeaders(['User-Agent' => 'BraisesEmeraude-site/1.0 (rendez-vous)'])->timeout(8)
                    ->get('https://nominatim.openstreetmap.org/search', ['q' => $query.', France', 'format' => 'json', 'limit' => 1, 'countrycodes' => 'fr'])
                    ->json(0);
            } catch (\Throwable) {
                return null;
            }

            return $hit ? [(float) $hit['lat'], (float) $hit['lon']] : null;
        });
    }

    /** Distance à vol d'oiseau en km (formule de haversine). */
    public static function km(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

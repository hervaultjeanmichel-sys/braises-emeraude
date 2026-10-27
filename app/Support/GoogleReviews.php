<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Store;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Avis Google des magasins via Places API (New).
 * Règles Google respectées : attribution (auteur, photo, lien), lien vers l'avis sur Google Maps,
 * lien de signalement, date de visite (France), mention du tri / filtrage, pas de stockage durable
 * (cache technique court, GOOGLE_REVIEWS_CACHE_MINUTES). En base : uniquement nos choix d'affichage.
 */
final class GoogleReviews
{
    private const FIELDS = 'id,displayName,rating,userRatingCount,googleMapsUri,reviews';

    public static function enabled(): bool
    {
        return filled(config('services.google_places.key'));
    }

    /**
     * @return ?array{rating: ?float, count: int, mapsUri: ?string, reviews: array<int, array>}
     */
    public static function forStore(Store $store, bool $withHidden = false): ?array
    {
        if (! self::enabled() || blank($store->google_place_id)) {
            return null;
        }
        $minutes = max(1, (int) config('services.google_places.cache_minutes', 60));
        $key = 'greviews:'.$store->google_place_id;
        $raw = Cache::get($key);
        if ($raw === null) {
            $raw = self::fetch($store->google_place_id) ?? false; // échec mémorisé 10 min : pas d'appel à chaque page
            Cache::put($key, $raw, $raw === false ? now()->addMinutes(10) : now()->addMinutes($minutes));
        }
        if (! $raw) {
            return null;
        }
        $choices = DB::table('google_review_choices')->where('store_id', $store->id)->get()->keyBy('review_key');
        $reviews = [];
        foreach ($raw['reviews'] ?? [] as $r) {
            $key = sha1((string) ($r['name'] ?? ($r['authorAttribution']['displayName'] ?? '').($r['publishTime'] ?? '')));
            $choice = $choices[$key] ?? null;
            $visible = $choice ? (bool) $choice->is_visible : true;
            if (! $visible && ! $withHidden) {
                continue;
            }
            $lang = $r['text']['languageCode'] ?? null;
            $origLang = $r['originalText']['languageCode'] ?? $lang;
            $reviews[] = [
                'key' => $key,
                'store_id' => $store->id,
                'store' => $store->short_name,
                'visible' => $visible,
                'featured' => $choice ? (bool) $choice->is_featured : false,
                'rating' => (int) round((float) ($r['rating'] ?? 0)),
                'text' => (string) ($r['text']['text'] ?? ''),
                'original' => (string) ($r['originalText']['text'] ?? ''),
                'translated' => $lang && $origLang && $lang !== $origLang,
                'author' => (string) ($r['authorAttribution']['displayName'] ?? 'Client Google'),
                'authorUri' => $r['authorAttribution']['uri'] ?? null,
                'photo' => $r['authorAttribution']['photoUri'] ?? null,
                'when' => (string) ($r['relativePublishTimeDescription'] ?? ''),
                'publishTime' => $r['publishTime'] ?? null,
                'visit' => self::visitLabel($r['visitDate'] ?? null),
                'mapsUri' => $r['googleMapsUri'] ?? ($raw['googleMapsUri'] ?? null),
                'flagUri' => $r['flagContentUri'] ?? null,
            ];
        }
        usort($reviews, fn ($a, $b) => [$b['featured'], $b['publishTime']] <=> [$a['featured'], $a['publishTime']]);

        return [
            'rating' => isset($raw['rating']) ? (float) $raw['rating'] : null,
            'count' => (int) ($raw['userRatingCount'] ?? 0),
            'mapsUri' => $raw['googleMapsUri'] ?? null,
            'reviews' => $reviews,
        ];
    }

    /** Synthèse du groupe : moyenne pondérée par le nombre d'avis de chaque magasin. */
    public static function summary(iterable $stores, bool $withHidden = false): ?array
    {
        $per = [];
        $reviews = [];
        foreach ($stores as $store) {
            if ($d = self::forStore($store, $withHidden)) {
                $per[] = ['store' => $store, 'rating' => $d['rating'], 'count' => $d['count'], 'mapsUri' => $d['mapsUri']];
                $reviews = [...$reviews, ...$d['reviews']];
            }
        }
        if ($per === []) {
            return null;
        }
        $count = array_sum(array_column($per, 'count'));
        $weighted = $count ? array_sum(array_map(fn ($p) => (float) $p['rating'] * $p['count'], $per)) / $count : null;
        usort($reviews, fn ($a, $b) => [$b['featured'], $b['publishTime']] <=> [$a['featured'], $a['publishTime']]);

        return ['rating' => $weighted ? round($weighted, 1) : null, 'count' => $count, 'stores' => $per, 'reviews' => $reviews];
    }

    public static function choose(int $storeId, string $key, array $values): void
    {
        DB::table('google_review_choices')->updateOrInsert(['store_id' => $storeId, 'review_key' => $key], $values + ['updated_at' => now(), 'created_at' => now()]);
    }

    public static function forget(Store $store): void
    {
        if ($store->google_place_id) {
            Cache::forget('greviews:'.$store->google_place_id);
        }
    }

    /** Recherche de l'identifiant Google d'un magasin (nom + adresse). @return array<int, array{id: string, name: string, address: string}> */
    public static function findPlace(string $query): array
    {
        $res = Http::withHeaders([
            'X-Goog-Api-Key' => (string) config('services.google_places.key'),
            'X-Goog-FieldMask' => 'places.id,places.displayName,places.formattedAddress',
        ])->timeout(15)->post('https://places.googleapis.com/v1/places:searchText', ['textQuery' => $query, 'languageCode' => 'fr', 'regionCode' => 'FR']);
        if (! $res->ok()) {
            throw new \RuntimeException('Google Places : '.$res->status().' '.mb_substr($res->body(), 0, 300));
        }

        return array_map(fn ($p) => ['id' => $p['id'], 'name' => $p['displayName']['text'] ?? '', 'address' => $p['formattedAddress'] ?? ''], $res->json('places') ?? []);
    }

    private static function fetch(string $placeId): ?array
    {
        try {
            $res = Http::withHeaders([
                'X-Goog-Api-Key' => (string) config('services.google_places.key'),
                'X-Goog-FieldMask' => self::FIELDS,
            ])->timeout(10)->get('https://places.googleapis.com/v1/places/'.rawurlencode($placeId), ['languageCode' => 'fr', 'regionCode' => 'FR']);
            if ($res->ok()) {
                return $res->json();
            }
            Log::warning('Avis Google indisponibles', ['place' => $placeId, 'status' => $res->status(), 'body' => mb_substr($res->body(), 0, 300)]);
        } catch (\Throwable $e) {
            Log::warning('Avis Google indisponibles', ['place' => $placeId, 'error' => $e->getMessage()]);
        }

        return null;
    }

    /** visitDate : {year, month[, day]} → « octobre 2026 ». */
    private static function visitLabel(mixed $d): ?string
    {
        if (! is_array($d) || empty($d['year'])) {
            return null;
        }
        $months = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

        return trim(($months[(int) ($d['month'] ?? 0)] ?? '').' '.$d['year']);
    }
}

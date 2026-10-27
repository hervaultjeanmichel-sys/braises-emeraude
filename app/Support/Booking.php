<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Appointment;
use App\Models\Store;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

/**
 * Prise de rendez-vous : réglages par magasin (stores.booking_settings) et calcul des créneaux libres.
 *
 * booking_settings = {
 *   types: [{key, label, duration (min), at_home (bool), enabled (bool)}],
 *   hours: [{day 1-7, opens "09:00", closes "12:00"}]   ← plages de RDV (sinon horaires du magasin, sinon défaut),
 *   min_delay_hours: 24, max_days: 45, step: 30,
 *   capacity_showroom: 1, capacity_home: 1,               ← RDV simultanés possibles,
 *   start_from: "2026-11-15",                             ← aucun RDV avant cette date (tous types),
 *   showroom_from: "2026-11-15",                          ← pas de RDV au showroom avant cette date (magasin pas encore ouvert),
 *   allow_showroom / allow_home / allow_sweep (bool)      ← catégories ouvertes en ligne,
 *   home_radius_km: 25                                    ← rayon d'intervention à domicile autour du magasin (0 = pas de contrôle)
 * }
 */
final class Booking
{
    public const TZ = 'Europe/Paris';

    /** Catégories de rendez-vous, activables par magasin. */
    public const KINDS = ['showroom' => 'Au showroom', 'domicile' => 'Visite technique à domicile', 'ramonage' => 'Ramonage à domicile'];

    /** Types proposés quand le magasin n'en a pas défini. */
    public const DEFAULT_TYPES = [
        ['key' => 'projet-showroom', 'label' => 'Nouveau projet — rendez-vous au showroom', 'duration' => 60, 'kind' => 'showroom', 'enabled' => true],
        ['key' => 'projet-domicile', 'label' => 'Nouveau projet — visite technique à domicile', 'duration' => 90, 'kind' => 'domicile', 'enabled' => true],
        ['key' => 'ramonage', 'label' => 'Ramonage — à domicile', 'duration' => 60, 'kind' => 'ramonage', 'enabled' => true],
    ];

    /** Plages par défaut : lun–ven 9h–12h et 14h–18h, sam 9h–12h. */
    public static function defaultHours(): array
    {
        $h = [];
        foreach (range(1, 5) as $d) {
            $h[] = ['day' => $d, 'opens' => '09:00', 'closes' => '12:00'];
            $h[] = ['day' => $d, 'opens' => '14:00', 'closes' => '18:00'];
        }
        $h[] = ['day' => 6, 'opens' => '09:00', 'closes' => '12:00'];

        return $h;
    }

    public function __construct(private Store $store) {}

    public static function for(Store $store): self
    {
        return new self($store);
    }

    public function settings(): array
    {
        $s = (array) ($this->store->booking_settings ?? []);

        return [
            'min_delay_hours' => (int) ($s['min_delay_hours'] ?? 24),
            'max_days' => max(1, (int) ($s['max_days'] ?? 45)),
            'step' => max(15, (int) ($s['step'] ?? 30)),
            'capacity_showroom' => max(1, (int) ($s['capacity_showroom'] ?? 1)),
            'capacity_home' => max(1, (int) ($s['capacity_home'] ?? 1)),
            'start_from' => $s['start_from'] ?? null,
            'showroom_from' => $s['showroom_from'] ?? null,
            'home_postcodes' => array_values(array_filter((array) ($s['home_postcodes'] ?? []))),
            'allow' => [
                'showroom' => (bool) ($s['allow_showroom'] ?? true),
                'domicile' => (bool) ($s['allow_home'] ?? true),
                'ramonage' => (bool) ($s['allow_sweep'] ?? true),
            ],
            'radius_km' => max(0, (int) ($s['home_radius_km'] ?? 0)),
            'hours' => array_values(array_filter((array) ($s['hours'] ?? []))) ?: ((array) $this->store->opening_hours ?: self::defaultHours()),
        ];
    }

    /** Catégorie d'un type (compatibilité avec les anciens réglages « at_home »). */
    public static function kindOf(array $t): string
    {
        if (isset($t['kind'], self::KINDS[$t['kind']])) {
            return $t['kind'];
        }
        if (str_contains(mb_strtolower((string) ($t['label'] ?? '')), 'ramon')) {
            return 'ramonage';
        }

        return ! empty($t['at_home']) ? 'domicile' : 'showroom';
    }

    /** @return array<string, array{key: string, label: string, duration: int, kind: string, at_home: bool}> */
    public function types(): array
    {
        $raw = array_values(array_filter((array) (($this->store->booking_settings ?? [])['types'] ?? [])));
        $out = [];
        foreach ($raw ?: self::DEFAULT_TYPES as $t) {
            if (array_key_exists('enabled', $t) && ! $t['enabled']) {
                continue;
            }
            $key = $t['key'] ?? null ?: Str::slug((string) ($t['label'] ?? ''));
            if (! $key || empty($t['label'])) {
                continue;
            }
            $kind = self::kindOf($t);
            if (! $this->settings()['allow'][$kind]) {
                continue;
            }
            $out[$key] = ['key' => $key, 'label' => (string) $t['label'], 'duration' => max(15, (int) ($t['duration'] ?? 60)), 'kind' => $kind, 'at_home' => $kind !== 'showroom'];
        }

        return $out;
    }

    public function isEnabled(): bool
    {
        return $this->store->is_published && $this->store->booking_enabled && $this->types() !== [];
    }

    /** Jours (Y-m-d) ayant au moins un créneau libre pour ce type. */
    public function days(string $typeKey, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now(self::TZ);
        $days = [];
        $set = $this->settings();
        $from = ($set['start_from'] && $set['start_from'] > $now->toDateString()) ? CarbonImmutable::parse($set['start_from'], self::TZ) : $now->startOfDay();
        for ($i = 0; $i <= $set['max_days']; $i++) {
            $date = $from->addDays($i)->toDateString();
            if ($this->slots($typeKey, $date, $now) !== []) {
                $days[] = $date;
            }
        }

        return $days;
    }

    /** Créneaux libres (« 09:00 ») d'un jour donné. */
    public function slots(string $typeKey, string $date, ?CarbonImmutable $now = null): array
    {
        $type = $this->types()[$typeKey] ?? null;
        if (! $type || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return [];
        }
        $set = $this->settings();
        $now ??= CarbonImmutable::now(self::TZ);
        $day = CarbonImmutable::parse($date, self::TZ)->startOfDay();

        $windowStart = ($set['start_from'] && $set['start_from'] > $now->toDateString()) ? CarbonImmutable::parse($set['start_from'], self::TZ) : $now->startOfDay();
        if ($day->gt($windowStart->addDays($set['max_days']))
            || in_array($date, (array) ($this->store->closed_dates ?? []), true)
            || ($set['start_from'] && $date < $set['start_from'])
            || (! $type['at_home'] && $set['showroom_from'] && $date < $set['showroom_from'])) {
            return [];
        }

        $earliest = $now->addHours($set['min_delay_hours']);
        $capacity = $type['at_home'] ? $set['capacity_home'] : $set['capacity_showroom'];
        $taken = Appointment::query()->blocking()
            ->where('store_id', $this->store->id)->where('at_home', $type['at_home'])
            ->whereBetween('starts_at', [$day->subDay(), $day->addDays(2)])
            ->get(['starts_at', 'ends_at']);

        $slots = [];
        foreach ($set['hours'] as $h) {
            if ((int) ($h['day'] ?? 0) !== $day->dayOfWeekIso || empty($h['opens']) || empty($h['closes'])) {
                continue;
            }
            $start = $day->setTimeFromTimeString($h['opens']);
            $close = $day->setTimeFromTimeString($h['closes']);
            for ($t = $start; $t->addMinutes($type['duration'])->lte($close); $t = $t->addMinutes($set['step'])) {
                $end = $t->addMinutes($type['duration']);
                if ($t->lt($earliest)) {
                    continue;
                }
                $overlap = $taken->filter(fn ($a) => $a->starts_at->lt($end) && $a->ends_at->gt($t))->count();
                if ($overlap < $capacity) {
                    $slots[] = $t->format('H:i');
                }
            }
        }
        sort($slots);

        return array_values(array_unique($slots));
    }

    public function isFree(string $typeKey, string $date, string $time): bool
    {
        return in_array($time, $this->slots($typeKey, $date), true);
    }

    /**
     * Zone d'intervention à domicile.
     * @return array{ok: ?bool, km: ?float, radius: int}  ok = null si l'adresse n'a pas pu être localisée (on laisse passer, l'équipe vérifie)
     */
    public function zone(?string $postcode, ?string $city = null, ?string $address = null): array
    {
        $radius = $this->settings()['radius_km'];
        if ($radius <= 0 || ! $this->store->latitude || ! $this->store->longitude) {
            return ['ok' => true, 'km' => null, 'radius' => $radius];
        }
        $point = Geo::locate(trim(implode(', ', array_filter([$address, trim($postcode.' '.$city)]))));
        if (! $point && $address) {
            $point = Geo::locate(trim($postcode.' '.$city)); // repli sur la commune
        }
        if (! $point) {
            return ['ok' => null, 'km' => null, 'radius' => $radius];
        }
        $km = round(Geo::km((float) $this->store->latitude, (float) $this->store->longitude, $point[0], $point[1]), 1);

        return ['ok' => $km <= $radius, 'km' => $km, 'radius' => $radius];
    }
}

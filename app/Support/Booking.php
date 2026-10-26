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
 *   showroom_from: "2026-11-15",                          ← pas de RDV au showroom avant cette date (magasin pas encore ouvert),
 *   home_postcodes: ["35000", …]                          ← zone des visites à domicile (vide = pas de contrôle)
 * }
 */
final class Booking
{
    public const TZ = 'Europe/Paris';

    /** Types proposés quand le magasin n'en a pas défini. */
    public const DEFAULT_TYPES = [
        ['key' => 'projet-showroom', 'label' => 'Nouveau projet — rendez-vous au showroom', 'duration' => 60, 'at_home' => false, 'enabled' => true],
        ['key' => 'projet-domicile', 'label' => 'Nouveau projet — visite technique à domicile', 'duration' => 90, 'at_home' => true, 'enabled' => true],
        ['key' => 'ramonage', 'label' => 'Ramonage — à domicile', 'duration' => 60, 'at_home' => true, 'enabled' => true],
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
            'showroom_from' => $s['showroom_from'] ?? null,
            'home_postcodes' => array_values(array_filter((array) ($s['home_postcodes'] ?? []))),
            'hours' => array_values(array_filter((array) ($s['hours'] ?? []))) ?: ((array) $this->store->opening_hours ?: self::defaultHours()),
        ];
    }

    /** @return array<string, array{key: string, label: string, duration: int, at_home: bool}> */
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
            $out[$key] = ['key' => $key, 'label' => (string) $t['label'], 'duration' => max(15, (int) ($t['duration'] ?? 60)), 'at_home' => (bool) ($t['at_home'] ?? false)];
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
        for ($i = 0; $i <= $this->settings()['max_days']; $i++) {
            $date = $now->startOfDay()->addDays($i)->toDateString();
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

        if ($day->gt($now->startOfDay()->addDays($set['max_days']))
            || in_array($date, (array) ($this->store->closed_dates ?? []), true)
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

    /** Code postal couvert pour une visite à domicile (pas de liste = pas de contrôle). */
    public function coversPostcode(?string $cp): bool
    {
        $list = $this->settings()['home_postcodes'];

        return $list === [] || in_array((string) $cp, $list, true);
    }
}

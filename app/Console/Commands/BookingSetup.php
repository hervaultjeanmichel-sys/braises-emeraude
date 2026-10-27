<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Store;
use App\Support\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/**
 * Prise de rendez-vous d'un magasin : diagnostic des créneaux, activation.
 *
 *   php artisan braises:booking rennes                         → état + prochains créneaux
 *   php artisan braises:booking rennes --enable                → active (types et plages par défaut si vides)
 *   php artisan braises:booking rennes --from=2026-11-15             → aucun RDV avant cette date
 *   php artisan braises:booking rennes --showroom-from=2026-11-16
 *   php artisan braises:booking rennes --radius=25 --home=on --sweep=off
 *   php artisan braises:booking rennes --disable
 */
class BookingSetup extends Command
{
    protected $signature = 'braises:booking {store : slug, nom court ou id} {--enable} {--disable} {--showroom-from= : AAAA-MM-JJ} {--from= : début du calendrier, tous types (AAAA-MM-JJ)} {--radius= : rayon d’intervention à domicile en km} {--showroom= : on|off} {--home= : on|off} {--sweep= : on|off}';

    protected $description = 'Active / diagnostique la prise de rendez-vous d’un magasin';

    public function handle(): int
    {
        $q = (string) $this->argument('store');
        $store = Store::query()->where('id', $q)->orWhere('slug', $q)->orWhere('short_name', $q)
            ->orWhere('slug', 'like', '%'.$q.'%')->first();
        if (! $store) {
            $this->error("Magasin « {$q} » introuvable");

            return self::FAILURE;
        }

        $settings = (array) ($store->booking_settings ?? []);
        if ($this->option('enable')) {
            $store->booking_enabled = true;
            $settings['types'] = $settings['types'] ?? Booking::DEFAULT_TYPES;
            $settings += ['min_delay_hours' => 24, 'max_days' => 45, 'step' => 30, 'capacity_showroom' => 1, 'capacity_home' => 1];
            if (empty($settings['hours']) && empty($store->opening_hours)) {
                $settings['hours'] = Booking::defaultHours();
            }
        }
        if ($this->option('disable')) {
            $store->booking_enabled = false;
        }
        if ($d = $this->option('showroom-from')) {
            $settings['showroom_from'] = CarbonImmutable::parse($d)->toDateString();
        }
        if (($r = $this->option('radius')) !== null) {
            $settings['home_radius_km'] = max(0, (int) $r);
        }
        foreach (['showroom' => 'allow_showroom', 'home' => 'allow_home', 'sweep' => 'allow_sweep'] as $opt => $key) {
            if (($v = $this->option($opt)) !== null) {
                $settings[$key] = in_array(strtolower((string) $v), ['on', 'oui', '1', 'true'], true);
            }
        }
        if ($d = $this->option('from')) {
            $settings['start_from'] = CarbonImmutable::parse($d)->toDateString();
        }
        $touched = collect(['enable', 'disable', 'showroom-from', 'from', 'radius', 'showroom', 'home', 'sweep'])->contains(fn ($o) => $this->option($o) !== null && $this->option($o) !== false);
        if ($touched) {
            $store->booking_settings = $settings;
            $store->save();
            $this->info('Enregistré.');
        }

        $b = Booking::for($store->fresh());
        $set = $b->settings();
        $this->line("<options=bold>{$store->name}</> — prise de RDV : ".($b->isEnabled() ? '<fg=green>ACTIVE</>' : '<fg=red>inactive</>'));
        $this->line('E-mail des demandes : '.($store->email ?: (s('leads.fallback_email') ?: '<fg=red>aucun (à renseigner !)</>')));
        $this->line("Calendrier à partir du ".($set['start_from'] ?: 'aujourd’hui')." · délai mini {$set['min_delay_hours']} h · jusqu'à {$set['max_days']} jours · pas {$set['step']} min · showroom à partir du ".($set['showroom_from'] ?: 'début du calendrier'));
        $this->line('Showroom : '.($set['allow']['showroom'] ? 'oui' : 'non').' · domicile : '.($set['allow']['domicile'] ? 'oui' : 'non').' · ramonage : '.($set['allow']['ramonage'] ? 'oui' : 'non').' · rayon : '.($set['radius_km'] ?: 'illimité').($set['radius_km'] ? ' km' : ''));
        foreach ($b->types() as $t) {
            $days = $b->days($t['key']);
            $first = $days[0] ?? null;
            $this->line(sprintf('  • %s (%d min, %s) : %d jour(s) disponible(s)%s', $t['label'], $t['duration'], \App\Support\Booking::KINDS[$t['kind']], count($days),
                $first ? ' — 1er : '.CarbonImmutable::parse($first)->locale('fr')->isoFormat('ddd D MMM').' → '.implode(' ', array_slice($b->slots($t['key'], $first), 0, 8)) : ''));
        }

        return self::SUCCESS;
    }
}

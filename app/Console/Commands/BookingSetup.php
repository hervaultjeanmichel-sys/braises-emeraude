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
 *   php artisan braises:booking rennes --showroom-from=2026-11-16
 *   php artisan braises:booking rennes --disable
 */
class BookingSetup extends Command
{
    protected $signature = 'braises:booking {store : slug, nom court ou id} {--enable} {--disable} {--showroom-from= : AAAA-MM-JJ}';

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
        if ($this->option('enable') || $this->option('disable') || $this->option('showroom-from')) {
            $store->booking_settings = $settings;
            $store->save();
            $this->info('Enregistré.');
        }

        $b = Booking::for($store->fresh());
        $set = $b->settings();
        $this->line("<options=bold>{$store->name}</> — prise de RDV : ".($b->isEnabled() ? '<fg=green>ACTIVE</>' : '<fg=red>inactive</>'));
        $this->line('E-mail des demandes : '.($store->email ?: (s('leads.fallback_email') ?: '<fg=red>aucun (à renseigner !)</>')));
        $this->line("Délai mini {$set['min_delay_hours']} h · jusqu'à {$set['max_days']} jours · pas {$set['step']} min · showroom à partir du ".($set['showroom_from'] ?: 'immédiatement'));
        foreach ($b->types() as $t) {
            $days = $b->days($t['key']);
            $first = $days[0] ?? null;
            $this->line(sprintf('  • %s (%d min, %s) : %d jour(s) disponible(s)%s', $t['label'], $t['duration'], $t['at_home'] ? 'domicile' : 'showroom', count($days),
                $first ? ' — 1er : '.CarbonImmutable::parse($first)->locale('fr')->isoFormat('ddd D MMM').' → '.implode(' ', array_slice($b->slots($t['key'], $first), 0, 8)) : ''));
        }

        return self::SUCCESS;
    }
}

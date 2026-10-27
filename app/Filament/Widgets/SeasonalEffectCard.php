<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\SeasonalEffect;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Schema;

/** Effet saisonnier en cours + prochain effet programmé. */
class SeasonalEffectCard extends Widget
{
    /** Réservé à l'administrateur du groupe (pas aux équipes magasin). */
    public static function canView(): bool
    {
        return (bool) auth()->user()?->isGroupAdmin();
    }

    protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.seasonal-effect-card';

    protected function getViewData(): array
    {
        if (! Schema::hasTable('seasonal_effects')) {
            return ['current' => null, 'next' => null, 'nextDate' => null];
        }
        $today = CarbonImmutable::now('Europe/Paris');
        $current = SeasonalEffect::current();
        $next = null;
        $nextDate = null;
        $effects = SeasonalEffect::where('is_active', true)->orderBy('sort')->get();
        for ($i = 1; $i <= 366 && ! $next; $i++) {
            $d = $today->addDays($i);
            $next = $effects->first(fn ($e) => $e->id !== $current?->id && $e->isOn($d));
            $nextDate = $next ? $d : null;
        }

        return ['current' => $current, 'next' => $next, 'nextDate' => $nextDate];
    }
}

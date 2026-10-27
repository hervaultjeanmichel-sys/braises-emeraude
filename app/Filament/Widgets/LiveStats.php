<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Brand;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Realisation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Chiffres clés en direct (actualisés toutes les 30 secondes). */
class LiveStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = now('Europe/Paris')->toDateString();
        $audience = Schema::hasTable('visits');
        $days = collect(range(13, 0))->map(fn ($i) => now('Europe/Paris')->subDays($i)->toDateString());
        $perDay = $audience ? DB::table('visits')->where('day', '>=', $days->first())->groupBy('day')->pluck(DB::raw('count(*)'), 'day') : collect();
        $visitors = (int) ($perDay[$today] ?? 0);
        $yesterday = (int) ($perDay[now('Europe/Paris')->subDay()->toDateString()] ?? 0);
        $online = $audience ? DB::table('visits')->where('last_seen_at', '>=', now()->subMinutes(5))->count() : 0;
        $views = $audience ? (int) DB::table('page_views')->where('day', $today)->sum('views') : 0;

        $leads7 = Lead::where('created_at', '>=', now()->subDays(7))->count();
        $leadsNew = Lead::where('status', 'nouvelle')->count();
        $rdvPending = Schema::hasTable('appointments') ? Appointment::where('status', 'en_attente')->count() : 0;
        $rdvNext = Schema::hasTable('appointments') ? Appointment::where('status', 'confirme')->where('starts_at', '>=', now())->count() : 0;

        return [
            Stat::make('Visiteurs aujourd’hui', number_format($visitors, 0, ',', ' '))
                ->description(($visitors >= $yesterday ? '↗ ' : '↘ ').'hier : '.$yesterday)
                ->descriptionColor($visitors >= $yesterday ? 'success' : 'warning')
                ->chart($days->map(fn ($d) => (int) ($perDay[$d] ?? 0))->all())->color('primary'),
            Stat::make('En ligne maintenant', (string) $online)->description('actifs depuis 5 min')->color('success'),
            Stat::make('Pages vues aujourd’hui', number_format($views, 0, ',', ' '))->description($visitors ? round($views / max(1, $visitors), 1).' pages par visiteur' : '—'),
            Stat::make('Demandes (7 jours)', (string) $leads7)->description($leadsNew.' à traiter')->descriptionColor($leadsNew ? 'warning' : 'success')->url('/admin/leads'),
            Stat::make('Rendez-vous en attente', (string) $rdvPending)->description($rdvNext.' confirmés à venir')->descriptionColor($rdvPending ? 'warning' : 'success')->url('/admin/appointments'),
            Stat::make('Produits en ligne', (string) Product::where('is_published', true)->count())->description(Product::where('is_published', false)->count().' masqués'),
            Stat::make('Marques présentes', (string) Brand::where('is_published', true)->whereHas('products', fn ($q) => $q->where('is_published', true))->count())->description(Brand::count().' au total'),
            Stat::make('Réalisations publiées', (string) (Schema::hasTable('realisations') ? Realisation::where('is_published', true)->count() : 0))
                ->description((Schema::hasTable('realisations') ? Realisation::where('review_consent', true)->whereNotNull('review_text')->count() : 0).' avec avis client'),
        ];
    }
}

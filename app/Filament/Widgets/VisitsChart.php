<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Visiteurs et pages vues sur 14 jours. */
class VisitsChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Visites — 14 derniers jours';

    protected static ?string $pollingInterval = '120s';

    protected static ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn ($i) => now('Europe/Paris')->subDays($i)->toDateString());
        $ok = Schema::hasTable('visits');
        $visitors = $ok ? DB::table('visits')->where('day', '>=', $days->first())->groupBy('day')->pluck(DB::raw('count(*)'), 'day') : collect();
        $views = $ok ? DB::table('page_views')->where('day', '>=', $days->first())->groupBy('day')->pluck(DB::raw('sum(views)'), 'day') : collect();

        return [
            'datasets' => [
                ['label' => 'Visiteurs', 'data' => $days->map(fn ($d) => (int) ($visitors[$d] ?? 0))->all(), 'borderColor' => '#8a5a16', 'backgroundColor' => 'rgba(138,90,22,.15)', 'fill' => true, 'tension' => .3],
                ['label' => 'Pages vues', 'data' => $days->map(fn ($d) => (int) ($views[$d] ?? 0))->all(), 'borderColor' => '#ff7b30', 'backgroundColor' => 'rgba(255,123,48,0)', 'tension' => .3],
            ],
            'labels' => $days->map(fn ($d) => \Carbon\Carbon::parse($d)->locale('fr')->isoFormat('dd D'))->all(),
        ];
    }
}

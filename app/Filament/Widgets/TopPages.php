<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Pages les plus vues et provenance des visiteurs (7 jours). */
class TopPages extends Widget
{
    protected static ?int $sort = 5;

    protected static string $view = 'filament.widgets.top-pages';

    protected static ?string $pollingInterval = '120s';

    protected function getViewData(): array
    {
        if (! Schema::hasTable('page_views')) {
            return ['pages' => collect(), 'sources' => collect(), 'devices' => collect()];
        }
        $from = now('Europe/Paris')->subDays(6)->toDateString();

        return [
            'pages' => DB::table('page_views')->where('day', '>=', $from)->groupBy('path')->selectRaw('path, sum(views) as v')->orderByDesc('v')->limit(8)->get(),
            'sources' => DB::table('visits')->where('day', '>=', $from)->groupBy('source')->selectRaw('source, count(*) as v')->orderByDesc('v')->get(),
            'devices' => DB::table('visits')->where('day', '>=', $from)->groupBy('device')->selectRaw('device, count(*) as v')->orderByDesc('v')->get(),
        ];
    }
}

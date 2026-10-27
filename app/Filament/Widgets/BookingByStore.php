<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Store;
use App\Support\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Cache;

/** Rendez-vous par magasin, avec l'interrupteur de prise de RDV en ligne. */
class BookingByStore extends TableWidget
{
    /** Réservé à l'administrateur du groupe (pas aux équipes magasin). */
    public static function canView(): bool
    {
        return (bool) auth()->user()?->isGroupAdmin();
    }

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Rendez-vous par magasin';

    protected static ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Store::query()->withCount([
                'appointments as pending_count' => fn ($q) => $q->where('status', 'en_attente'),
                'appointments as upcoming_count' => fn ($q) => $q->where('status', 'confirme')->where('starts_at', '>=', now()),
                'appointments as month_count' => fn ($q) => $q->where('created_at', '>=', now()->subDays(30)),
                'appointments as home_count' => fn ($q) => $q->where('created_at', '>=', now()->subDays(30))->where('at_home', true),
            ]))
            ->defaultSort('sort')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('area_label')->label('Magasin')->state(fn (Store $s) => $s->area_label ?: $s->short_name)->weight('bold')
                    ->description(fn (Store $s) => $s->email ?: '⚠ aucun e-mail pour recevoir les demandes'),
                Tables\Columns\ToggleColumn::make('booking_enabled')->label('RDV en ligne')
                    ->afterStateUpdated(fn () => Cache::flush()),
                Tables\Columns\TextColumn::make('offre')->label('Proposé')->state(function (Store $s) {
                    $a = Booking::for($s)->settings()['allow'];
                    $r = Booking::for($s)->settings()['radius_km'];

                    return collect(['Showroom' => $a['showroom'], 'Domicile' => $a['domicile'], 'Ramonage' => $a['ramonage']])->filter()->keys()->implode(' · ').($r && ($a['domicile'] || $a['ramonage']) ? ' — '.$r.' km' : '');
                })->color('gray'),
                Tables\Columns\TextColumn::make('pending_count')->label('En attente')->badge()->color(fn ($state) => $state ? 'warning' : 'gray')
                    ->url(fn (Store $s) => '/admin/appointments?tableFilters[status][value]=en_attente&tableFilters[store_id][value]='.$s->id),
                Tables\Columns\TextColumn::make('upcoming_count')->label('Confirmés à venir')->badge()->color('success'),
                Tables\Columns\TextColumn::make('month_count')->label('Demandes 30 j'),
                Tables\Columns\TextColumn::make('home_count')->label('dont à domicile'),
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Support\BookingMailer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Demandes';

    protected static ?string $modelLabel = 'rendez-vous';

    protected static ?string $pluralModelLabel = 'Rendez-vous';

    protected static ?int $navigationSort = 2;

    /** Une équipe magasin ne voit que ses rendez-vous ; l'admin groupe voit tout. */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        return ($user && ! $user->isGroupAdmin() && $user->store_id) ? $query->where('store_id', $user->store_id) : $query;
    }

    public static function getNavigationBadge(): ?string
    {
        $n = static::getEloquentQuery()->where('status', 'en_attente')->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Rendez-vous')->description('Pour prévenir le client, utilisez les boutons « Confirmer » ou « Refuser » de la liste.')->columns(2)->schema([
                Forms\Components\Select::make('store_id')->label('Magasin')->relationship('store', 'short_name')->required(),
                Forms\Components\Select::make('status')->label('Statut')->options(Appointment::STATUSES)->required(),
                Forms\Components\TextInput::make('type_label')->label('Type')->required(),
                Forms\Components\Toggle::make('at_home')->label('À domicile'),
                Forms\Components\DateTimePicker::make('starts_at')->label('Début')->seconds(false)->required(),
                Forms\Components\DateTimePicker::make('ends_at')->label('Fin')->seconds(false)->required(),
            ]),
            Forms\Components\Section::make('Client')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nom')->required(),
                Forms\Components\TextInput::make('phone')->label('Téléphone')->tel()->required(),
                Forms\Components\TextInput::make('email')->label('E-mail')->email()->required(),
                Forms\Components\TextInput::make('address')->label('Adresse'),
                Forms\Components\TextInput::make('postal_code')->label('Code postal'),
                Forms\Components\TextInput::make('city')->label('Commune'),
                Forms\Components\Textarea::make('message')->label('Message du client')->rows(3)->columnSpanFull()->disabled(),
                Forms\Components\Textarea::make('team_note')->label('Note de l’équipe (ajoutée à l’e-mail de confirmation ou de refus)')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')->label('Date')->dateTime('D d/m/Y · H:i')->sortable(),
                Tables\Columns\TextColumn::make('store.short_name')->label('Magasin'),
                Tables\Columns\TextColumn::make('type_label')->label('Type')->wrap(),
                Tables\Columns\IconColumn::make('at_home')->label('Domicile')->boolean(),
                Tables\Columns\TextColumn::make('name')->label('Client')->searchable()->description(fn (Appointment $a) => $a->phone),
                Tables\Columns\TextColumn::make('city')->label('Commune')->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => Appointment::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) { 'en_attente' => 'warning', 'confirme' => 'success', 'refuse', 'annule' => 'danger', default => 'gray' }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(Appointment::STATUSES),
                Tables\Filters\SelectFilter::make('store_id')->label('Magasin')->relationship('store', 'short_name'),
                Tables\Filters\Filter::make('a_venir')->label('À venir')->default()->query(fn (Builder $q) => $q->where('starts_at', '>=', now()->startOfDay())),
            ])
            ->defaultSort('starts_at')
            ->actions([
                Tables\Actions\Action::make('confirmer')->label('Confirmer')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Appointment $a) => $a->status === 'en_attente')
                    ->form([Forms\Components\Textarea::make('team_note')->label('Message ajouté à la confirmation (facultatif)')->rows(3)])
                    ->action(function (Appointment $a, array $data) {
                        $a->update(['status' => 'confirme', 'confirmed_at' => now(), 'team_note' => $data['team_note'] ?? $a->team_note]);
                        BookingMailer::confirmed($a);
                        Notification::make()->title('Rendez-vous confirmé, e-mail envoyé au client')->success()->send();
                    }),
                Tables\Actions\Action::make('refuser')->label('Refuser')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn (Appointment $a) => $a->status === 'en_attente')
                    ->form([Forms\Components\Textarea::make('team_note')->label('Motif ou autre proposition (envoyé au client)')->rows(3)])
                    ->requiresConfirmation()
                    ->action(function (Appointment $a, array $data) {
                        $a->update(['status' => 'refuse', 'team_note' => $data['team_note'] ?? null]);
                        BookingMailer::refused($a);
                        Notification::make()->title('Demande refusée, e-mail envoyé au client')->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}

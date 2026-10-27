<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonalEffectResource\Pages;
use App\Models\SeasonalEffect;
use Carbon\CarbonImmutable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonalEffectResource extends Resource
{
    protected static ?string $model = SeasonalEffect::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'effet saisonnier';

    protected static ?string $pluralModelLabel = 'Effets saisonniers';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nom (affiché sur le bouton « ✕ … »)')->required()->placeholder('Noël'),
                Forms\Components\Select::make('effect')->label('Effet')->options(SeasonalEffect::EFFECTS)->required(),
                Forms\Components\DatePicker::make('starts_on')->label('Début')->required(),
                Forms\Components\DatePicker::make('ends_on')->label('Fin (incluse)')->required()->afterOrEqual(fn (Forms\Get $get) => $get('yearly') ? null : 'starts_on'),
                Forms\Components\Toggle::make('yearly')->label('Revient chaque année aux mêmes dates')->default(true)
                    ->helperText('Seuls le jour et le mois comptent (ex. 31/12 → 03/01 fonctionne à cheval sur deux années). Pâques change de date : décochez.'),
                Forms\Components\Select::make('intensity')->label('Intensité')->options(SeasonalEffect::INTENSITIES)->default('medium')->required(),
                Forms\Components\Select::make('scope')->label('Où ?')->options(['all' => 'Tout le site', 'home' => 'Accueil uniquement'])->default('all')->required(),
                Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
                Forms\Components\TextInput::make('sort')->label('Priorité (le plus petit l’emporte si deux effets se chevauchent)')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $today = CarbonImmutable::now('Europe/Paris');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('effect')->label('Effet')->formatStateUsing(fn ($state) => SeasonalEffect::EFFECTS[$state] ?? $state),
                Tables\Columns\TextColumn::make('starts_on')->label('Du')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('ends_on')->label('Au')->date('d/m/Y'),
                Tables\Columns\IconColumn::make('yearly')->label('Annuel')->boolean(),
                Tables\Columns\TextColumn::make('en_cours')->label('Aujourd’hui')->badge()
                    ->state(fn (SeasonalEffect $e) => $e->isOn($today) ? 'En cours' : '—')
                    ->color(fn ($state) => $state === 'En cours' ? 'success' : 'gray'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Actif'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeasonalEffects::route('/'),
            'create' => Pages\CreateSeasonalEffect::route('/create'),
            'edit' => Pages\EditSeasonalEffect::route('/{record}/edit'),
        ];
    }
}

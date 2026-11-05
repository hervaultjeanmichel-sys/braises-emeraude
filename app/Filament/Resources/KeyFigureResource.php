<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\KeyFigureResource\Pages;
use App\Models\KeyFigure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/* Bandeau « Chiffres clés » (ronds avant le pied de page, toutes les pages).
 * Titre et sur-titre de la section : Réglages › Textes du site (groupe « figures »). */
class KeyFigureResource extends Resource
{
    protected static ?string $model = KeyFigure::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'chiffre clé';

    protected static ?string $pluralModelLabel = 'Chiffres clés';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Chiffre')->columns(3)->schema([
                Forms\Components\TextInput::make('prefix')->label('Avant le chiffre')->placeholder('Depuis, Plus de…')->maxLength(40),
                Forms\Components\TextInput::make('value')->label('Chiffre')->numeric()->minValue(0)->required()->default(0)
                    ->helperText('Ignoré si la source est automatique.'),
                Forms\Components\TextInput::make('suffix')->label('Après le chiffre')->placeholder('+, %…')->maxLength(10),
                Forms\Components\TextInput::make('label')->label('Libellé sous le rond')->required()->maxLength(60)->columnSpan(2),
                Forms\Components\Select::make('source')->label('Source du chiffre')->options(KeyFigure::SOURCES)->default('manual')->required(),
                Forms\Components\TextInput::make('sort')->label('Ordre')->numeric()->default(0),
                Forms\Components\Toggle::make('animate')->label('Compteur animé')->default(true)
                    ->helperText('À désactiver pour une année (ex. 2013).'),
                Forms\Components\Toggle::make('is_active')->label('Affiché')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prefix')->label('Avant'),
                Tables\Columns\TextColumn::make('value')->label('Chiffre')
                    ->formatStateUsing(fn ($state, KeyFigure $record) => $record->source === 'manual' ? $state : $record->displayValue().' (auto)'),
                Tables\Columns\TextColumn::make('suffix')->label('Après'),
                Tables\Columns\TextColumn::make('label')->label('Libellé'),
                Tables\Columns\TextColumn::make('sort')->label('Ordre')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Affiché'),
            ])
            ->defaultSort('sort', 'asc')
            ->reorderable('sort')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKeyFigures::route('/'),
            'create' => Pages\CreateKeyFigure::route('/create'),
            'edit' => Pages\EditKeyFigure::route('/{record}/edit'),
        ];
    }
}

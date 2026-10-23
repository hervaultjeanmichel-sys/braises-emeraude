<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EventBannerResource\Pages;
use App\Models\EventBanner;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventBannerResource extends Resource
{
    protected static ?string $model = EventBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'bandeau';

    protected static ?string $pluralModelLabel = 'Bandeaux d\'événement';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Bandeau')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->label('Titre')->required(),
                Forms\Components\TextInput::make('text')->label('Texte'),
                Forms\Components\TextInput::make('cta_label')->label('Bouton'),
                Forms\Components\TextInput::make('cta_url')->label('Lien'),
                Forms\Components\DateTimePicker::make('starts_at')->label('Début'),
                Forms\Components\DateTimePicker::make('ends_at')->label('Fin'),
                Forms\Components\Select::make('store_id')->label('Magasin')->relationship('store', 'short_name'),
                Forms\Components\Toggle::make('is_active')->label('Actif'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Bandeau'),
                Tables\Columns\TextColumn::make('starts_at')->label('Début')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('ends_at')->label('Fin')->dateTime('d/m/Y H:i'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Actif'),
            ])
            ->filters([
                
            ])
            ->defaultSort('starts_at', 'desc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventBanners::route('/'),
            'create' => Pages\CreateEventBanner::route('/create'),
            'edit' => Pages\EditEventBanner::route('/{record}/edit'),
        ];
    }
}

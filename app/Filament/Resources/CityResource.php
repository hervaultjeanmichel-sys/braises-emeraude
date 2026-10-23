<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'page ville';

    protected static ?string $pluralModelLabel = 'Pages villes';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ville')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Ville')->required(),
                SeoFields::slug()->placeholder('poele-a-bois-granules-dinard'),
                Forms\Components\TextInput::make('postal_code')->label('Code postal'),
                Forms\Components\Select::make('store_id')->label('Magasin le plus proche')->relationship('store', 'short_name')->required(),
                Forms\Components\TagsInput::make('neighbours')->label('Communes voisines'),
                Forms\Components\FileUpload::make('hero_image')->image()->directory('villes'),
            ]),
            Forms\Components\Section::make('Texte propre à la ville')->description('Pas de copier-coller d\'une ville à l\'autre.')
                ->schema([...SeoFields::content(), SeoFields::wordCount(600), SeoFields::faq()]),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Ville')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('store.short_name')->label('Magasin'),
                Tables\Columns\TextColumn::make('slug')->label('URL')->prefix('/')->suffix('/'),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
            ])
            ->filters([
                
            ])
            ->defaultSort('name', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}

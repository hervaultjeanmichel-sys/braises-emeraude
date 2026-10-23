<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $modelLabel = 'produit';

    protected static ?string $pluralModelLabel = 'Produits';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Produit')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nom')->required(),
                SeoFields::slug('/produit/'),
                Forms\Components\Select::make('brand_id')->label('Marque')->relationship('brand', 'name')->searchable()->preload(),
                Forms\Components\Select::make('product_category_id')->label('Catégorie')->relationship('category', 'name')->searchable()->preload(),
                Forms\Components\Select::make('energy')->label('Énergie')->options(['bois' => 'Bois', 'granules' => 'Granulés', 'mixte' => 'Mixte', 'gaz' => 'Gaz']),
                Forms\Components\Select::make('appliance_type')->label('Type d\'appareil')->options(['poele' => 'Poêle', 'insert' => 'Insert', 'foyer' => 'Foyer', 'cuisiniere' => 'Cuisinière']),
                Forms\Components\TextInput::make('power_kw')->label('Puissance (kW)')->numeric()->step(0.1),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('surface_min_m2')->label('Surface chauffée min (m²)')->numeric(),
                    Forms\Components\TextInput::make('surface_max_m2')->label('Surface chauffée max (m²)')->numeric(),
                ]),
                Forms\Components\Toggle::make('is_featured')->label('Mis en avant'),
            ]),
            Forms\Components\Section::make('Médias')->schema([
                Forms\Components\FileUpload::make('images')->label('Photos (converties en WebP)')->image()->multiple()->reorderable()->directory('produits')->imageEditor(),
                Forms\Components\FileUpload::make('datasheet')->label('Fiche technique PDF')->acceptedFileTypes(['application/pdf'])->directory('fiches'),
            ]),
            Forms\Components\Section::make('Texte de la fiche')->schema([
                ...SeoFields::content(false),
                Forms\Components\Textarea::make('excerpt')->label('Résumé (cartes produit)')->rows(2),
                Forms\Components\Repeater::make('specs')->label('Caractéristiques techniques')->schema([
                    Forms\Components\TextInput::make('label')->label('Caractéristique')->required(),
                    Forms\Components\TextInput::make('value')->label('Valeur')->required(),
                ])->columns(2)->defaultItems(0)->collapsed(),
            ]),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Produit')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('brand.name')->label('Marque')->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Catégorie'),
                Tables\Columns\TextColumn::make('energy')->label('Énergie')->badge(),
                Tables\Columns\TextColumn::make('power_kw')->label('kW')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')->label('Marque')->relationship('brand', 'name'),
                Tables\Filters\SelectFilter::make('energy')->label('Énergie')->options(['bois' => 'Bois', 'granules' => 'Granulés', 'mixte' => 'Mixte', 'gaz' => 'Gaz']),
                Tables\Filters\SelectFilter::make('product_category_id')->label('Catégorie')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_published')->label('Publié'),
            ])
            ->defaultSort('name', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

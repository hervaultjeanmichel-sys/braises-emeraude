<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Models\ProductCategory;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $modelLabel = 'catégorie';

    protected static ?string $pluralModelLabel = 'Catégories';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Catégorie')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nom')->required(),
                Forms\Components\Select::make('parent_id')->label('Catégorie parente')->relationship('parent', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('slug')->label('Segment d\'URL')->required()->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule, Forms\Get $get) => $rule->where('parent_id', $get('parent_id')))
                    ->helperText('L\'URL complète /categorie-produit/parent/segment/ est calculée automatiquement.'),
                Forms\Components\TextInput::make('sort')->label('Ordre')->numeric()->default(0),
                Forms\Components\FileUpload::make('image')->image()->directory('categories'),
                Forms\Components\Select::make('icon')->label('Icône (carte univers)')->options(['bois' => 'Flamme bois', 'granules' => 'Poêle granulés', 'mixte' => 'Double flamme', 'cuisson' => 'Cuisson']),
                Forms\Components\Textarea::make('card_text')->label('Texte de la carte univers')->rows(2)->maxLength(400),
                Forms\Components\TextInput::make('brands_line')->label('Ligne des marques')->helperText('Vide = calculée automatiquement depuis le catalogue.'),
            ]),
            Forms\Components\Section::make('Texte de la catégorie')->schema(SeoFields::content(false)),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Catégorie')->searchable(),
                Tables\Columns\TextColumn::make('path')->label('URL')->prefix('/categorie-produit/')->suffix('/'),
                Tables\Columns\TextColumn::make('products_count')->label('Produits')->counts('products'),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
            ])
            ->filters([
                
            ])
            ->defaultSort('path', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}

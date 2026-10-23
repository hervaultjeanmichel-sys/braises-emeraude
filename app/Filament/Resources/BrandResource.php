<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $modelLabel = 'marque';

    protected static ?string $pluralModelLabel = 'Marques';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Marque')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nom')->required(),
                SeoFields::slug('/marque/'),
                Forms\Components\TextInput::make('country')->label('Pays'),
                Forms\Components\TextInput::make('website')->label('Site officiel')->url(),
                Forms\Components\FileUpload::make('logo')->image()->directory('marques'),
                Forms\Components\TextInput::make('sort')->label('Ordre')->numeric()->default(0),
            ]),
            Forms\Components\Section::make('Page marque')->schema([...SeoFields::content(false), SeoFields::wordCount(400)]),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->height(32),
                Tables\Columns\TextColumn::make('name')->label('Marque')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('products_count')->label('Produits')->counts('products')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
            ])
            ->filters([
                
            ])
            ->defaultSort('sort', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}

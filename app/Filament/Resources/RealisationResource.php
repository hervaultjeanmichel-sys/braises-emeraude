<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RealisationResource\Pages;
use App\Models\Realisation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RealisationResource extends Resource
{
    protected static ?string $model = Realisation::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'réalisation';

    protected static ?string $pluralModelLabel = 'Réalisations';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Le chantier')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->label('Titre')->required()->placeholder('Poêle à granulés MCZ Ego à Dinard')->columnSpanFull(),
                Forms\Components\TextInput::make('city')->label('Commune')->placeholder('Dinard'),
                Forms\Components\Select::make('store_id')->label('Magasin')->relationship('store', 'short_name'),
                Forms\Components\Select::make('product_id')->label('Appareil installé')->relationship('product', 'name')->searchable()->preload(),
                Forms\Components\Select::make('energy')->label('Énergie')->options(Realisation::ENERGIES),
                Forms\Components\DatePicker::make('installed_on')->label('Date d’installation'),
                Forms\Components\FileUpload::make('photos')->label('Photos (converties en WebP)')->image()->multiple()->reorderable()->directory('realisations')->imageEditor()->maxSize(12000)->helperText('La première photo sert de vignette.')->columnSpanFull(),
                Forms\Components\Textarea::make('description')->label('Description courte')->rows(3)->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Avis du client')->description('Uniquement un vrai avis de ce client, avec son accord. Jamais d’avis rédigé à sa place.')->columns(2)->schema([
                Forms\Components\Textarea::make('review_text')->label('Avis (texte exact du client)')->rows(4)->columnSpanFull(),
                Forms\Components\TextInput::make('review_author')->label('Signature')->placeholder('Marie L.')->helperText('Prénom et initiale du nom.'),
                Forms\Components\Select::make('review_rating')->label('Note')->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★']),
                Forms\Components\DatePicker::make('review_date')->label('Date de l’avis'),
                Forms\Components\Select::make('review_source')->label('Source')->options(Realisation::SOURCES),
                Forms\Components\Toggle::make('review_consent')->label('Le client a donné son accord écrit pour publier son avis et les photos')->helperText('Sans cet accord, l’avis n’apparaît pas sur le site.')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Publication')->columns(3)->schema([
                Forms\Components\Toggle::make('is_published')->label('Publiée'),
                Forms\Components\Toggle::make('is_featured')->label('Mise en avant'),
                Forms\Components\TextInput::make('sort')->label('Ordre')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photos')->label('Photo')->disk('public')->limit(1)->square(),
                Tables\Columns\TextColumn::make('title')->label('Titre')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('city')->label('Commune')->searchable(),
                Tables\Columns\TextColumn::make('store.short_name')->label('Magasin'),
                Tables\Columns\TextColumn::make('review_rating')->label('Avis')->formatStateUsing(fn ($state) => $state ? str_repeat('★', (int) $state) : '—'),
                Tables\Columns\IconColumn::make('review_consent')->label('Accord')->boolean(),
                Tables\Columns\ToggleColumn::make('is_published')->label('Publiée'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('store_id')->label('Magasin')->relationship('store', 'short_name'),
                Tables\Filters\TernaryFilter::make('is_published')->label('Publiée'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRealisations::route('/'),
            'create' => Pages\CreateRealisation::route('/create'),
            'edit' => Pages\EditRealisation::route('/{record}/edit'),
        ];
    }
}

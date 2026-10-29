<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PromoSlideResource\Pages;
use App\Models\PromoSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoSlideResource extends Resource
{
    protected static ?string $model = PromoSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'volet promo';

    protected static ?string $pluralModelLabel = 'Volets promo';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contenu du volet')->description('Le volet glisse depuis la gauche de l\'écran. Le visiteur peut le fermer ; il ne réapparaît pas avant la fréquence choisie.')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nom interne')->required()->placeholder('Ouverture Rennes — remise MCZ')->helperText('Visible seulement dans l\'admin.')->columnSpanFull(),
                Forms\Components\FileUpload::make('image')->label('Image (convertie en WebP)')->image()->imageEditor()->directory('promos')->maxSize(12000)->helperText('Visuel de l\'offre. Format portrait ou carré conseillé.'),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('image_alt')->label('Description de l\'image')->helperText('Pour l\'accessibilité et Google. Si l\'image contient le texte de l\'offre, recopiez-le ici.'),
                    Forms\Components\ColorPicker::make('bg_color')->label('Couleur de fond')->default('#34e3d3')->required()->helperText('La couleur du texte s\'adapte automatiquement (claire ou foncée).'),
                ]),
                Forms\Components\TextInput::make('eyebrow')->label('Sur-titre')->placeholder('Nouveau magasin'),
                Forms\Components\TextInput::make('title')->label('Titre')->placeholder('Braises Émeraude ouvre à Rennes'),
                Forms\Components\Textarea::make('text')->label('Texte')->rows(3)->columnSpanFull()->placeholder('500 € de remise sur les appareils MCZ pour l\'ouverture.'),
                Forms\Components\TextInput::make('cta_label')->label('Bouton'),
                Forms\Components\TextInput::make('cta_url')->label('Lien du bouton')->placeholder('/poele-a-bois-granules-rennes/'),
            ]),
            Forms\Components\Section::make('Affichage')->columns(2)->schema([
                Forms\Components\DateTimePicker::make('starts_at')->label('Début d\'affichage')->seconds(false)->helperText('Vide = tout de suite.'),
                Forms\Components\DateTimePicker::make('ends_at')->label('Fin d\'affichage')->seconds(false)->after('starts_at')->helperText('Vide = sans fin.'),
                Forms\Components\Select::make('scope')->label('Pages')->options(PromoSlide::SCOPES)->default('all')->selectablePlaceholder(false),
                Forms\Components\Select::make('frequency')->label('Fréquence pour un même visiteur')->options(PromoSlide::FREQUENCIES)->default('day')->selectablePlaceholder(false),
                Forms\Components\TextInput::make('delay')->label('Apparition après (secondes)')->numeric()->minValue(0)->maxValue(60)->default(3),
                Forms\Components\TextInput::make('sort')->label('Ordre (si plusieurs volets en même temps)')->numeric()->default(0)->helperText('Un seul volet s\'affiche : le plus petit ordre gagne.'),
                Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->disk('public')->height(48),
                Tables\Columns\ColorColumn::make('bg_color')->label('Fond'),
                Tables\Columns\TextColumn::make('name')->label('Volet')->searchable(),
                Tables\Columns\TextColumn::make('starts_at')->label('Début')->dateTime('d/m/Y H:i')->placeholder('—'),
                Tables\Columns\TextColumn::make('ends_at')->label('Fin')->dateTime('d/m/Y H:i')->placeholder('—'),
                Tables\Columns\TextColumn::make('scope')->label('Pages')->formatStateUsing(fn ($state) => PromoSlide::SCOPES[$state] ?? $state),
                Tables\Columns\ToggleColumn::make('is_active')->label('Actif'),
            ])
            ->defaultSort('starts_at', 'desc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoSlides::route('/'),
            'create' => Pages\CreatePromoSlide::route('/create'),
            'edit' => Pages\EditPromoSlide::route('/{record}/edit'),
        ];
    }
}

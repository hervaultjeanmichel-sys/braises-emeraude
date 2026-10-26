<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Models\Store;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Magasins';

    protected static ?string $modelLabel = 'magasin';

    protected static ?string $pluralModelLabel = 'Magasins';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Magasin')->columnSpanFull()->tabs([
                Forms\Components\Tabs\Tab::make('Coordonnées')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')->label('Nom complet')->required(),
                        Forms\Components\TextInput::make('short_name')->label('Nom court (menu)')->required(),
                        Forms\Components\TextInput::make('area_label')->label('Secteur affiché')->placeholder('Dinard · Pleurtuit'),
                        Forms\Components\TextInput::make('region_label')->label('Pastille région (carte d\'accueil)')->placeholder('Côte d\'Émeraude'),
                        Forms\Components\TextInput::make('announcement')->label('Annonce (si pas encore de téléphone)')->placeholder('Inauguration en novembre 2026'),
                        Forms\Components\Textarea::make('card_text')->label('Texte de la carte d\'accueil')->rows(2)->maxLength(400)->columnSpanFull(),
                        SeoFields::slug(),
                        Forms\Components\TextInput::make('address')->label('Adresse'),
                        Forms\Components\TextInput::make('postal_code')->label('Code postal'),
                        Forms\Components\TextInput::make('city')->label('Ville'),
                        Forms\Components\TextInput::make('phone')->label('Téléphone')->tel(),
                        Forms\Components\TextInput::make('email')->label('E-mail de réception des demandes')->email(),
                        Forms\Components\TextInput::make('google_business_url')->label('Fiche Google Business')->url(),
                        Forms\Components\TextInput::make('latitude')->label('Latitude')->numeric()->helperText('Pour la carte : clic droit sur le magasin dans Google Maps, puis cliquer sur les coordonnées pour les copier (ex. 48.6107).'),
                        Forms\Components\TextInput::make('longitude')->label('Longitude')->numeric()->helperText('Ex. -2.0546 (négatif à l\'ouest de Greenwich).'),
                        Forms\Components\Toggle::make('is_new')->label('Badge « Nouveau »'),
                        Forms\Components\TextInput::make('badge_label')->label('Texte du badge')->placeholder('Nouveau'),
                        Forms\Components\TextInput::make('sort')->label('Ordre')->numeric()->default(0),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make('Horaires')->schema([
                    Forms\Components\Repeater::make('opening_hours')->label('Plages d\'ouverture')
                        ->schema([
                            Forms\Components\Select::make('day')->label('Jour')->options([1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'])->required(),
                            Forms\Components\TimePicker::make('opens')->label('Ouvre')->seconds(false)->format('H:i')->required(),
                            Forms\Components\TimePicker::make('closes')->label('Ferme')->seconds(false)->format('H:i')->required(),
                        ])->columns(3)->defaultItems(0)->reorderable(),
                    Forms\Components\TagsInput::make('closed_dates')->label('Jours de fermeture exceptionnelle (AAAA-MM-JJ)'),
                ]),
                Forms\Components\Tabs\Tab::make('Page magasin')->schema([
                    ...SeoFields::content(),
                    SeoFields::wordCount(600),
                    Forms\Components\TagsInput::make('served_communes')->label('Communes desservies (affichées)'),
                    Forms\Components\TagsInput::make('served_postcodes')->label('Codes postaux routés vers ce magasin'),
                    Forms\Components\FileUpload::make('photos')->label('Photos du showroom')->image()->multiple()->reorderable()->directory('magasins')->imageEditor(),
                    Forms\Components\Repeater::make('team')->label('Équipe')->schema([
                        Forms\Components\TextInput::make('name')->label('Prénom Nom')->required(),
                        Forms\Components\TextInput::make('role')->label('Rôle'),
                        Forms\Components\FileUpload::make('photo')->image()->directory('equipe'),
                    ])->columns(3)->defaultItems(0)->collapsed(),
                    Forms\Components\CheckboxList::make('brands')->label('Marques exposées')->relationship('brands', 'name')->columns(3),
                ]),
                Forms\Components\Tabs\Tab::make('Rendez-vous')->schema([
                    Forms\Components\Toggle::make('booking_enabled')->label('Prise de rendez-vous en ligne active'),
                    Forms\Components\TextInput::make('google_calendar_id')->label('Identifiant Google Agenda du magasin'),
                    Forms\Components\Group::make([
                    Forms\Components\TextInput::make('min_delay_hours')->label('Délai minimum avant un RDV (heures)')->numeric()->default(48),
                    Forms\Components\Repeater::make('types')->label('Types de rendez-vous')->schema([
                        Forms\Components\TextInput::make('label')->label('Libellé')->required(),
                        Forms\Components\TextInput::make('duration')->label('Durée (min)')->numeric()->required(),
                        Forms\Components\Toggle::make('at_home')->label('À domicile'),
                    ])->columns(3)->defaultItems(0),
                    Forms\Components\TagsInput::make('home_postcodes')->label('Codes postaux couverts pour les visites à domicile'),
                    ])->statePath('booking_settings'),
                ]),
                Forms\Components\Tabs\Tab::make('SEO')->schema([SeoFields::section()]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Magasin')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone'),
                Tables\Columns\IconColumn::make('is_new')->label('Nouveau')->boolean(),
                Tables\Columns\IconColumn::make('booking_enabled')->label('RDV')->boolean(),
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}

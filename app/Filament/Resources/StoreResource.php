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
                        Forms\Components\TextInput::make('zone_label')->label('Zone (titre de la carte d\'accueil)')->placeholder('Zone Saint-Malo'),
                        Forms\Components\TextInput::make('zone_text')->label('Communes couvertes')->placeholder('Tout Saint-Malo, Cancale, jusqu\'à Combourg.')->maxLength(400),
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
                        Forms\Components\TextInput::make('google_place_id')->label('Identifiant Google (Place ID)')->helperText('Pour afficher les avis Google. Trouvé automatiquement par : php artisan braises:google-reviews {magasin} --find'),
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
                    Forms\Components\FileUpload::make('photos')->label('Galerie du showroom (3 à 10 photos)')->image()->multiple()->reorderable()->maxFiles(10)->directory('magasins')->imageEditor()->maxSize(12000)
                        ->helperText('Converties en WebP 1600 px. La 1re photo est la plus grande dans la galerie.'),
                    Forms\Components\Fieldset::make('Vidéo du showroom (facultative)')->columns(3)->schema([
                        Forms\Components\FileUpload::make('video')->label('Fichier vidéo (mp4/webm, 40 Mo max)')->directory('magasins/video')->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(40000),
                        Forms\Components\TextInput::make('video_url')->label('… ou lien YouTube / Vimeo')->url()->placeholder('https://youtu.be/…'),
                        Forms\Components\FileUpload::make('video_poster')->label('Image d’attente')->image()->directory('magasins'),
                    ]),
                    Forms\Components\Repeater::make('team')->label('Équipe')->schema([
                        Forms\Components\TextInput::make('name')->label('Prénom Nom')->required(),
                        Forms\Components\TextInput::make('role')->label('Rôle'),
                        Forms\Components\FileUpload::make('photo')->image()->directory('equipe'),
                    ])->columns(3)->defaultItems(0)->collapsed(),
                    Forms\Components\CheckboxList::make('brands')->label('Marques exposées')->relationship('brands', 'name')->columns(3),
                ]),
                Forms\Components\Tabs\Tab::make('Rendez-vous')->schema([
                    Forms\Components\Toggle::make('booking_enabled')->label('Prise de rendez-vous en ligne (interrupteur général)')
                        ->helperText('Les demandes arrivent « en attente » dans Demandes › Rendez-vous : l’équipe confirme ou refuse, le client est prévenu par e-mail.'),
                    Forms\Components\Group::make([
                        Forms\Components\Fieldset::make('Ce que le magasin propose en ligne')->columns(4)->schema([
                            Forms\Components\Toggle::make('allow_showroom')->label('RDV au showroom')->default(true)->live(),
                            Forms\Components\Toggle::make('allow_home')->label('Visites à domicile')->default(true)->live(),
                            Forms\Components\Toggle::make('allow_sweep')->label('Ramonage')->default(true)->live(),
                            Forms\Components\TextInput::make('home_radius_km')->label('Rayon d’intervention (km)')->numeric()->minValue(0)->default(25)->suffix('km')
                                ->helperText('Autour du magasin, pour domicile et ramonage. 0 = pas de limite.')
                                ->visible(fn (Forms\Get $get) => $get('allow_home') || $get('allow_sweep')),
                        ]),
                        Forms\Components\Repeater::make('types')->label('Types de rendez-vous')->schema([
                            Forms\Components\TextInput::make('label')->label('Libellé affiché')->required()->columnSpan(2),
                            Forms\Components\Select::make('kind')->label('Catégorie')->options(\App\Support\Booking::KINDS)->required()->default('showroom'),
                            Forms\Components\TextInput::make('duration')->label('Durée (min)')->numeric()->required()->default(60),
                            Forms\Components\Toggle::make('enabled')->label('Proposé')->default(true),
                            Forms\Components\Hidden::make('key'),
                        ])->columns(5)->default(\App\Support\Booking::DEFAULT_TYPES)->reorderable()
                            ->itemLabel(fn (array $state) => $state['label'] ?? null)
                            ->helperText('Un type n’est proposé que si sa catégorie est ouverte ci-dessus. Vide = types par défaut : nouveau projet au showroom (1 h), visite technique à domicile (1 h 30), ramonage (1 h).'),
                        Forms\Components\Repeater::make('hours')->label('Plages de rendez-vous')->schema([
                            Forms\Components\Select::make('day')->label('Jour')->options([1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'])->required(),
                            Forms\Components\TimePicker::make('opens')->label('De')->seconds(false)->required(),
                            Forms\Components\TimePicker::make('closes')->label('À')->seconds(false)->required(),
                        ])->columns(3)->defaultItems(0)->collapsed()
                            ->helperText('Vide = horaires d’ouverture du magasin (ou lun–ven 9h–12h / 14h–18h et sam 9h–12h s’ils ne sont pas renseignés).'),
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('min_delay_hours')->label('Délai minimum (heures)')->numeric()->default(24),
                            Forms\Components\TextInput::make('max_days')->label('Réservable jusqu’à (jours)')->numeric()->default(45),
                            Forms\Components\Select::make('step')->label('Pas entre deux créneaux')->options([15 => '15 min', 30 => '30 min', 60 => '1 h'])->default(30),
                            Forms\Components\TextInput::make('capacity_showroom')->label('RDV simultanés au showroom')->numeric()->default(1),
                            Forms\Components\TextInput::make('capacity_home')->label('Interventions simultanées à domicile')->numeric()->default(1),
                            Forms\Components\DatePicker::make('start_from')->label('Calendrier ouvert à partir du')->helperText('Aucun rendez-vous avant cette date (tous types).'),
                            Forms\Components\DatePicker::make('showroom_from')->label('RDV au showroom à partir du')->helperText('Pour un magasin pas encore ouvert.'),
                        ]),
                    ])->statePath('booking_settings'),
                    Forms\Components\TextInput::make('google_calendar_id')->label('Identifiant Google Agenda du magasin')->helperText('Branchement Google Agenda : étape suivante.'),
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

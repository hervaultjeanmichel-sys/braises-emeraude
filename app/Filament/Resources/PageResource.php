<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'page';

    protected static ?string $pluralModelLabel = 'Pages';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Page')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->label('Titre (admin, menu)')->required(),
                Forms\Components\TextInput::make('slug')->label('Slug (URL)')->required()->unique(ignoreRecord: true)
                    ->helperText('« / » pour l\'accueil. Sinon : chauffage-bois, showroom…'),
                Forms\Components\Select::make('template')->label('Gabarit')->options([
                    'home' => 'Accueil Groupe', 'default' => 'Page standard', 'univers' => 'Univers produit',
                    'contact' => 'Contact', 'realisations' => 'Réalisations', 'legal' => 'Mentions / légal',
                ])->default('default')->required(),
                Forms\Components\Toggle::make('in_sitemap')->label('Dans le sitemap')->default(true),
                Forms\Components\FileUpload::make('hero_image')->label('Image d\'en-tête')->image()->directory('pages'),
            ]),
            Forms\Components\Section::make('Contenu')->schema([...SeoFields::content(), Forms\Components\Builder::make('sections')->label('Sections de la page')->columnSpanFull()->collapsible()->collapsed()
                ->helperText('Astuce titres : entourez un mot d\'astérisques (*une seule*) pour le mettre en couleur émeraude.')
                ->blocks([
                Forms\Components\Builder\Block::make('saga')->label('Bandeau cinéma (épisodes)')->icon('heroicon-o-film')->schema([
                    Forms\Components\TextInput::make('kicker')->label('Mention en haut à gauche')->default('Braises Émeraude présente'),
                    Forms\Components\FileUpload::make('bg_video')->label('Vidéo de fond commune (optionnelle)')->helperText('Une seule vidéo derrière tous les épisodes : remplace les décors animés. MP4/WebM sans son, 20 Mo max. Chargée après l\'affichage de la page.')->directory('accueil')->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(20000),
                    Forms\Components\FileUpload::make('bg_poster')->label('Image d\'attente de la vidéo commune')->helperText('Affichée pendant le chargement et pour les visiteurs en économie de données. WebP conseillé.')->image()->directory('accueil'),
                    Forms\Components\Repeater::make('episodes')->label('Épisodes')->schema([
                        Forms\Components\TextInput::make('label')->label('Numéro')->placeholder('Épisode I'),
                        Forms\Components\TextInput::make('title')->label('Titre')->required(),
                        Forms\Components\Textarea::make('tagline')->label('Accroche')->rows(2),
                        Forms\Components\TextInput::make('rating')->label('Pastille')->placeholder('Énergie 100 % renouvelable'),
                        Forms\Components\Select::make('scene')->label('Décor')->options(['fire' => 'Feu animé', 'stove' => 'Poêle animé', 'forest' => 'Forêt animée', 'video' => 'Vidéo'])->default('fire')->live(),
                        Forms\Components\FileUpload::make('video')->label('Vidéo (webm/mp4, sans son, compressée)')->directory('accueil')->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(12000)->visible(fn (Forms\Get $get) => $get('scene') === 'video'),
                        Forms\Components\FileUpload::make('poster')->label('Image d\'attente de la vidéo')->image()->directory('accueil')->visible(fn (Forms\Get $get) => $get('scene') === 'video'),
                        Forms\Components\TextInput::make('temp')->label('Température affichée (vidéo)')->placeholder('21°')->visible(fn (Forms\Get $get) => $get('scene') === 'video'),
                    ])->columns(2)->collapsed()->itemLabel(fn (array $state) => $state['title'] ?? null)->maxItems(5),
                    Forms\Components\TextInput::make('h1')->label('Titre H1 (bas du bandeau)')->required(),
                    Forms\Components\Textarea::make('billing')->label('Générique (texte sous le H1)')->rows(2)->helperText('*mot* = gras.'),
                    Forms\Components\TextInput::make('cta_label')->label('Bouton'),
                    Forms\Components\TextInput::make('cta_url')->label('Lien du bouton')->default('/devis/'),
                ]),
                Forms\Components\Builder\Block::make('stores')->label('Les magasins (cartes dynamiques)')->icon('heroicon-o-building-storefront')->schema([
                    Forms\Components\Toggle::make('emblem')->label('Emblème du logo en filigrane')->default(true),
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\Textarea::make('text')->label('Texte')->rows(3),
                    Forms\Components\TextInput::make('link_label')->label('Lien des cartes')->default('Voir le magasin de :magasin')->helperText(':magasin est remplacé par le nom court.'),
                    Forms\Components\Toggle::make('show_map')->label('Afficher la carte des magasins')->default(true)->helperText('Carte OpenStreetMap des magasins dont la latitude / longitude est renseignée.'),
                ]),
                Forms\Components\Builder\Block::make('categories')->label('Univers produits (cartes dynamiques)')->icon('heroicon-o-squares-2x2')->schema([
                    Forms\Components\Toggle::make('emblem')->label('Emblème du logo en filigrane')->default(false),
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\TextInput::make('more_label')->label('Lien des cartes')->default('Découvrir →'),
                    Forms\Components\Toggle::make('light')->label('Fond clair')->default(true),
                ]),
                Forms\Components\Builder\Block::make('quote')->label('Signature de campagne (parallaxe)')->icon('heroicon-o-megaphone')->schema([
                    Forms\Components\TextInput::make('big')->label('Phrase principale')->required(),
                    Forms\Components\TextInput::make('small')->label('Phrase secondaire'),
                    Forms\Components\TextInput::make('hand')->label('Mot manuscrit'),
                    Forms\Components\FileUpload::make('image')->label('Image de fond (WebP)')->image()->directory('accueil'),
                    Forms\Components\TextInput::make('image_alt')->label('Description de l\'image'),
                ]),
                Forms\Components\Builder\Block::make('marquee')->label('Bandeau des marques (dynamique)')->icon('heroicon-o-tag')->schema([
                    Forms\Components\TextInput::make('aria')->label('Libellé accessibilité')->default('Nos marques'),
                ]),
                Forms\Components\Builder\Block::make('steps')->label('Étapes / méthode')->icon('heroicon-o-list-bullet')->schema([
                    Forms\Components\Toggle::make('emblem')->label('Emblème du logo en filigrane')->default(false),
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\Repeater::make('items')->label('Étapes')->schema([
                        Forms\Components\TextInput::make('title')->label('Titre H3')->required(),
                        Forms\Components\Textarea::make('text')->label('Texte')->rows(2),
                    ])->columns(2)->defaultItems(0)->collapsed()->itemLabel(fn (array $state) => $state['title'] ?? null),
                ]),
                Forms\Components\Builder\Block::make('aids')->label('Encart aides & financement')->icon('heroicon-o-banknotes')->schema([
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\Textarea::make('text')->label('Texte')->rows(3),
                    Forms\Components\TextInput::make('cta_label')->label('Bouton'),
                    Forms\Components\TextInput::make('cta_url')->label('Lien'),
                    Forms\Components\TagsInput::make('bullets')->label('Points listés'),
                ]),
                Forms\Components\Builder\Block::make('seo_faq')->label('Texte SEO + FAQ de la page')->icon('heroicon-o-document-text')->schema([
                    Forms\Components\Toggle::make('emblem')->label('Emblème du logo en filigrane')->default(true),
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\RichEditor::make('body')->label('Texte (H3, gras, liens)')->toolbarButtons(['h3', 'bold', 'italic', 'bulletList', 'link']),
                    Forms\Components\TextInput::make('faq_title')->label('Titre de la FAQ')->default('Vos questions'),
                ]),
                Forms\Components\Builder\Block::make('final')->label('Appel final (fond braise)')->icon('heroicon-o-fire')->schema([
                    Forms\Components\TextInput::make('hand')->label('Mot manuscrit'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\TextInput::make('cta_label')->label('Bouton principal'),
                    Forms\Components\TextInput::make('cta_url')->label('Lien principal')->default('/devis/'),
                    Forms\Components\TextInput::make('cta2_label')->label('Bouton secondaire'),
                    Forms\Components\TextInput::make('cta2_url')->label('Lien secondaire'),
                ]),
                Forms\Components\Builder\Block::make('text')->label('Texte libre')->icon('heroicon-o-pencil')->schema([
                    Forms\Components\Toggle::make('emblem')->label('Emblème du logo en filigrane')->default(false),
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2'),
                    Forms\Components\RichEditor::make('body')->label('Texte')->toolbarButtons(['h3', 'bold', 'italic', 'bulletList', 'link']),
                    Forms\Components\FileUpload::make('image')->image()->directory('pages'),
                ]),
            ]), SeoFields::faq()]),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Page')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('URL')->formatStateUsing(fn ($state) => $state === '/' ? '/' : '/'.$state.'/'),
                Tables\Columns\TextColumn::make('template')->label('Gabarit')->badge(),
                Tables\Columns\TextColumn::make('robots')->label('Robots'),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
            ])
            ->filters([
                
            ])
            ->defaultSort('title', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}

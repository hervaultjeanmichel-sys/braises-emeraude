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
            Forms\Components\Section::make('Contenu')->schema([...SeoFields::content(), Forms\Components\Builder::make('sections')->label('Sections de la page')->columnSpanFull()->collapsible()->blocks([
                Forms\Components\Builder\Block::make('hero')->label('Bandeau d\'accueil')->schema([
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre')->required(),
                    Forms\Components\TextInput::make('title_accent')->label('Fin du titre en couleur'),
                    Forms\Components\Textarea::make('lead')->label('Accroche')->rows(3),
                    Forms\Components\TextInput::make('cta_label')->label('Bouton principal'),
                    Forms\Components\TextInput::make('cta_url')->label('Lien du bouton'),
                    Forms\Components\TextInput::make('sticker')->label('Pastille manuscrite'),
                    Forms\Components\Repeater::make('trust')->label('Chiffres clés')->schema([
                        Forms\Components\TextInput::make('value')->label('Valeur')->required(),
                        Forms\Components\TextInput::make('label')->label('Libellé')->required(),
                    ])->columns(2)->defaultItems(0),
                    Forms\Components\Repeater::make('slides')->label('Diaporama')->schema([
                        Forms\Components\FileUpload::make('image')->image()->directory('accueil')->required(),
                        Forms\Components\TextInput::make('caption')->label('Légende'),
                    ])->columns(2)->defaultItems(0),
                ]),
                Forms\Components\Builder\Block::make('stores')->label('Les 3 magasins (dynamique)')->schema([
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\Textarea::make('text')->label('Texte')->rows(3),
                ]),
                Forms\Components\Builder\Block::make('categories')->label('Univers produits (dynamique)')->schema([
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\Textarea::make('text')->label('Texte')->rows(3),
                ]),
                Forms\Components\Builder\Block::make('brands')->label('Marques (dynamique)')->schema([
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                ]),
                Forms\Components\Builder\Block::make('text')->label('Texte libre')->schema([
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2'),
                    Forms\Components\RichEditor::make('body')->label('Texte')->toolbarButtons(['h3', 'bold', 'italic', 'bulletList', 'link']),
                    Forms\Components\FileUpload::make('image')->image()->directory('pages'),
                ]),
                Forms\Components\Builder\Block::make('steps')->label('Étapes / arguments')->schema([
                    Forms\Components\TextInput::make('eyebrow')->label('Sur-titre'),
                    Forms\Components\TextInput::make('title')->label('Titre H2')->required(),
                    Forms\Components\Repeater::make('items')->label('Éléments')->schema([
                        Forms\Components\TextInput::make('title')->label('Titre H3')->required(),
                        Forms\Components\Textarea::make('text')->label('Texte')->rows(2),
                    ])->columns(2)->defaultItems(0),
                ]),
                Forms\Components\Builder\Block::make('cta')->label('Appel à l\'action')->schema([
                    Forms\Components\TextInput::make('title')->label('Titre')->required(),
                    Forms\Components\Textarea::make('text')->label('Texte')->rows(2),
                    Forms\Components\TextInput::make('button_label')->label('Bouton'),
                    Forms\Components\TextInput::make('button_url')->label('Lien'),
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

<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'article';

    protected static ?string $pluralModelLabel = 'Blog';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Article')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->label('Titre')->required(),
                Forms\Components\TextInput::make('slug')->label('Slug')->required()->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->helperText('URL : /AAAA/MM/JJ/slug/ (date de publication).'),
                Forms\Components\DateTimePicker::make('published_at')->label('Date de publication')->required()->default(now()),
                Forms\Components\Select::make('store_id')->label('Magasin concerné')->relationship('store', 'short_name'),
                Forms\Components\FileUpload::make('image')->label('Image à la une')->image()->directory('blog'),
                Forms\Components\Textarea::make('excerpt')->label('Résumé')->rows(2),
            ]),
            Forms\Components\Section::make('Contenu')->schema([...SeoFields::content(false), SeoFields::faq()]),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('published_at')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Titre')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('store.short_name')->label('Magasin'),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
            ])
            ->filters([
                
            ])
            ->defaultSort('published_at', 'desc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

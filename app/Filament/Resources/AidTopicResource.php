<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AidTopicResource\Pages;
use App\Models\AidTopic;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AidTopicResource extends Resource
{
    protected static ?string $model = AidTopic::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Contenus';

    protected static ?string $modelLabel = 'page aide';

    protected static ?string $pluralModelLabel = 'Aides';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Aide')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->label('Titre')->required(),
                SeoFields::slug('/aides/'),
                Forms\Components\DatePicker::make('verified_on')->label('Mis à jour le')->required(),
                Forms\Components\TextInput::make('source_url')->label('Source officielle')->url()->default('https://france-renov.gouv.fr/'),
                Forms\Components\TextInput::make('sort')->label('Ordre')->numeric()->default(0),
            ]),
            Forms\Components\Section::make('Contenu')->schema([
                ...SeoFields::content(),
                Forms\Components\Repeater::make('amounts')->label('Montants et conditions')->schema([
                    Forms\Components\TextInput::make('label')->label('Dispositif / profil')->required(),
                    Forms\Components\TextInput::make('amount')->label('Montant'),
                    Forms\Components\TextInput::make('condition')->label('Condition'),
                ])->columns(3)->defaultItems(0),
                Forms\Components\Textarea::make('financing_example')->label('Exemple de financement chiffré')->rows(4),
                SeoFields::faq(),
            ]),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Aide')->searchable(),
                Tables\Columns\TextColumn::make('verified_on')->label('Vérifiée le')->date('d/m/Y')->sortable()
                    ->color(fn ($state) => $state && $state->lt(now()->subYear()) ? 'danger' : null),
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
            'index' => Pages\ListAidTopics::route('/'),
            'create' => Pages\CreateAidTopic::route('/create'),
            'edit' => Pages\EditAidTopic::route('/{record}/edit'),
        ];
    }
}

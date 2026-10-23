<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?string $modelLabel = 'réglage';

    protected static ?string $pluralModelLabel = 'Textes du site';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Réglage')->schema([
                Forms\Components\TextInput::make('key')->label('Clé')->required()->unique(ignoreRecord: true)->disabledOn('edit'),
                Forms\Components\Select::make('group')->label('Groupe')->options(['general' => 'Général', 'header' => 'En-tête', 'footer' => 'Pied de page', 'seo' => 'SEO', 'devis' => 'Formulaire de devis', 'social' => 'Réseaux sociaux'])->default('general'),
                Forms\Components\Textarea::make('value')->label('Valeur')->rows(4),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')->label('Groupe')->badge(),
                Tables\Columns\TextColumn::make('key')->label('Clé')->searchable(),
                Tables\Columns\TextColumn::make('value')->label('Valeur')->limit(80)->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')->label('Groupe')->options(['general' => 'Général', 'header' => 'En-tête', 'footer' => 'Pied de page', 'seo' => 'SEO', 'devis' => 'Formulaire de devis', 'social' => 'Réseaux sociaux']),
            ])
            ->defaultSort('key', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}

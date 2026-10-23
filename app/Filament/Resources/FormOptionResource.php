<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\FormOptionResource\Pages;
use App\Models\FormOption;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormOptionResource extends Resource
{
    protected static ?string $model = FormOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?string $modelLabel = 'option de formulaire';

    protected static ?string $pluralModelLabel = 'Listes du formulaire';

    protected static ?int $navigationSort = 2;

    public const LISTS = [
        'project_type' => '1 · Votre projet',
        'appliance_type' => '2 · Type d\'appareil',
        'energy' => '3 · Énergie',
        'replacement' => '4 · Remplacement',
        'floor' => '5 · Étage',
        'budget' => '7 · Budget',
        'delay' => '7 · Délai',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Option')->columns(2)->schema([
                Forms\Components\Select::make('list')->label('Liste')->options(self::LISTS)->required(),
                Forms\Components\TextInput::make('label')->label('Libellé affiché')->required(),
                Forms\Components\TextInput::make('value')->label('Valeur technique')->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule, Forms\Get $get) => $rule->where('list', $get('list'))),
                Forms\Components\TextInput::make('sort')->label('Ordre')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('list')->label('Liste')->formatStateUsing(fn ($state) => self::LISTS[$state] ?? $state)->badge(),
                Tables\Columns\TextColumn::make('label')->label('Libellé'),
                Tables\Columns\TextColumn::make('sort')->label('Ordre')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('list')->label('Liste')->options(self::LISTS),
            ])
            ->defaultSort('sort', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormOptions::route('/'),
            'create' => Pages\CreateFormOption::route('/create'),
            'edit' => Pages\EditFormOption::route('/{record}/edit'),
        ];
    }
}

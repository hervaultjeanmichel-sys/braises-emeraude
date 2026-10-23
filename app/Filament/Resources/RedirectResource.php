<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use App\Filament\Support\SeoFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-right';

    protected static ?string $navigationGroup = 'SEO';

    protected static ?string $modelLabel = 'redirection';

    protected static ?string $pluralModelLabel = 'Redirections 301';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Redirection')->columns(2)->schema([
                Forms\Components\TextInput::make('from_path')->label('Ancienne URL')->required()->unique(ignoreRecord: true)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('from_path', \App\Models\Redirect::normalize((string) $state)))
                    ->dehydrateStateUsing(fn (string $state) => \App\Models\Redirect::normalize($state)),
                Forms\Components\TextInput::make('to_path')->label('Nouvelle URL')->required(),
                Forms\Components\Select::make('status_code')->label('Code')->options([301 => '301 permanente', 302 => '302 temporaire', 410 => '410 supprimée'])->default(301),
                Forms\Components\TextInput::make('note')->label('Note'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_path')->label('Ancienne URL')->searchable(),
                Tables\Columns\TextColumn::make('to_path')->label('Vers')->searchable(),
                Tables\Columns\TextColumn::make('status_code')->label('Code'),
                Tables\Columns\TextColumn::make('hits')->label('Appels')->sortable(),
                Tables\Columns\TextColumn::make('last_hit_at')->label('Dernier appel')->since(),
            ])
            ->filters([
                
            ])
            ->defaultSort('from_path', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}

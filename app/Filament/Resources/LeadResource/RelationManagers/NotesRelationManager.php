<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeadResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/** Historique des échanges avec le client. */
class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    protected static ?string $title = 'Historique des échanges';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')->label('Note')->rows(3)->required()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('user.name')->label('Par'),
                Tables\Columns\TextColumn::make('body')->label('Note')->wrap(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Ajouter une note')
                    ->mutateFormDataUsing(fn (array $data) => [...$data, 'user_id' => auth()->id()]),
            ])
            ->actions([Tables\Actions\DeleteAction::make()]);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers\NotesRelationManager;
use App\Models\FormOption;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Demandes centralisées. Une équipe magasin ne voit que ses demandes ;
 * le compte groupe voit tout.
 */
class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Demandes';

    protected static ?string $modelLabel = 'demande';

    protected static ?string $pluralModelLabel = 'Demandes';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        return ($user && ! $user->isGroupAdmin() && $user->store_id)
            ? $query->where('store_id', $user->store_id)
            : $query;
    }

    public static function getNavigationBadge(): ?string
    {
        $n = static::getEloquentQuery()->where('status', 'nouvelle')->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        $opt = fn (string $list) => FormOption::listFor($list);

        return $form->schema([
            Forms\Components\Section::make('Suivi')->columns(3)->schema([
                Forms\Components\Select::make('status')->label('Statut')->options(Lead::STATUSES)->required(),
                Forms\Components\Select::make('store_id')->label('Magasin')->relationship('store', 'short_name')
                    ->disabled(fn () => ! auth()->user()?->isGroupAdmin()),
                Forms\Components\Select::make('type')->label('Type')->options(Lead::TYPES)->disabled(),
                Forms\Components\TextInput::make('source')->label('Source')->disabled(),
                Forms\Components\DateTimePicker::make('created_at')->label('Reçue le')->disabled(),
                Forms\Components\TextInput::make('openfire_id')->label('N° OpenFire')->disabled(),
            ]),
            Forms\Components\Section::make('Client')->columns(3)->schema([
                Forms\Components\TextInput::make('name')->label('Nom')->required(),
                Forms\Components\TextInput::make('phone')->label('Téléphone')->tel(),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('postal_code')->label('Code postal'),
                Forms\Components\Textarea::make('message')->label('Message')->rows(3)->columnSpan(2),
            ]),
            Forms\Components\Section::make('Projet')->columns(3)->collapsible()->schema([
                Forms\Components\Select::make('project_type')->label('Projet')->options($opt('project_type')),
                Forms\Components\Select::make('appliance_type')->label('Appareil')->options($opt('appliance_type')),
                Forms\Components\Select::make('energy')->label('Énergie')->options($opt('energy')),
                Forms\Components\Select::make('replacement')->label('Remplacement')->options($opt('replacement')),
                Forms\Components\TextInput::make('surface_m2')->label('Surface (m²)')->numeric(),
                Forms\Components\Select::make('has_flue')->label('Conduit existant')->options(['oui' => 'Oui', 'non' => 'Non', 'nsp' => 'Je ne sais pas']),
                Forms\Components\Select::make('floor')->label('Étage')->options($opt('floor')),
                Forms\Components\Select::make('brand_id')->label('Marque souhaitée')->relationship('brand', 'name'),
                Forms\Components\Select::make('budget')->label('Budget')->options($opt('budget')),
                Forms\Components\Select::make('delay')->label('Délai')->options($opt('delay')),
                Forms\Components\Repeater::make('products')->label('Sélection « Mon projet »')->schema([
                    Forms\Components\TextInput::make('name')->label('Produit'),
                    Forms\Components\TextInput::make('brand')->label('Marque'),
                    Forms\Components\TextInput::make('note')->label('Remarque'),
                ])->columns(3)->disabled()->columnSpanFull(),
                Forms\Components\FileUpload::make('photos')->label('Photos du lieu')->image()->multiple()->disabled()->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Préparé par l\'IA — à valider')->collapsible()->schema([
                Forms\Components\Select::make('ai_priority')->label('Priorité estimée')->options(['haute' => 'Haute', 'moyenne' => 'Moyenne', 'basse' => 'Basse']),
                Forms\Components\Textarea::make('ai_summary')->label('Résumé')->rows(3),
                Forms\Components\Textarea::make('ai_missing')->label('Informations manquantes')->rows(2),
                Forms\Components\Textarea::make('ai_draft')->label('Brouillon de réponse (jamais envoyé sans validation)')->rows(8),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Reçue')->dateTime('d/m H:i')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Client')->searchable()
                    ->description(fn (Lead $r) => trim(($r->postal_code ?? '').' · '.($r->phone ?? ''), ' ·')),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge()->formatStateUsing(fn ($state) => Lead::TYPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('store.short_name')->label('Magasin')->sortable(),
                Tables\Columns\TextColumn::make('ai_priority')->label('Priorité')->badge()
                    ->color(fn ($state) => match ($state) { 'haute' => 'danger', 'moyenne' => 'warning', default => 'gray' }),
                Tables\Columns\SelectColumn::make('status')->label('Statut')->options(Lead::STATUSES),
                Tables\Columns\TextColumn::make('source')->label('Source')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(Lead::STATUSES),
                Tables\Filters\SelectFilter::make('type')->label('Type')->options(Lead::TYPES),
                Tables\Filters\SelectFilter::make('store_id')->label('Magasin')->relationship('store', 'short_name')
                    ->visible(fn () => auth()->user()?->isGroupAdmin()),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getRelations(): array
    {
        return [NotesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}

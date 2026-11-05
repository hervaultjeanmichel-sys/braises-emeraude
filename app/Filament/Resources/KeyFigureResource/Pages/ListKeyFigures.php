<?php

declare(strict_types=1);

namespace App\Filament\Resources\KeyFigureResource\Pages;

use App\Filament\Resources\KeyFigureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeyFigures extends ListRecords
{
    protected static string $resource = KeyFigureResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

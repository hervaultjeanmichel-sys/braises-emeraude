<?php

declare(strict_types=1);

namespace App\Filament\Resources\AidTopicResource\Pages;

use App\Filament\Resources\AidTopicResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAidTopics extends ListRecords
{
    protected static string $resource = AidTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\EventBannerResource\Pages;

use App\Filament\Resources\EventBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventBanners extends ListRecords
{
    protected static string $resource = EventBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

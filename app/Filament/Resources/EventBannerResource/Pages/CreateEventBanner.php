<?php

declare(strict_types=1);

namespace App\Filament\Resources\EventBannerResource\Pages;

use App\Filament\Resources\EventBannerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventBanner extends CreateRecord
{
    protected static string $resource = EventBannerResource::class;
}

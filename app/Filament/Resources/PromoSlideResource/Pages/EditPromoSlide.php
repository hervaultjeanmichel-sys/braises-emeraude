<?php

declare(strict_types=1);

namespace App\Filament\Resources\PromoSlideResource\Pages;

use App\Filament\Resources\PromoSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromoSlide extends EditRecord
{
    protected static string $resource = PromoSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

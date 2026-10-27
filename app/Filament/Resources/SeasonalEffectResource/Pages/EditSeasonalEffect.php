<?php

declare(strict_types=1);

namespace App\Filament\Resources\SeasonalEffectResource\Pages;

use App\Filament\Resources\SeasonalEffectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeasonalEffect extends EditRecord
{
    protected static string $resource = SeasonalEffectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

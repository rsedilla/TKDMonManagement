<?php

namespace App\Filament\Resources\ConsolidationResource\Pages;

use App\Filament\Resources\ConsolidationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewConsolidation extends ViewRecord
{
    protected static string $resource = ConsolidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

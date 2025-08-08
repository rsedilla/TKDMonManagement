<?php

namespace App\Filament\Resources\CellGroupResource\Pages;

use App\Filament\Resources\CellGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCellGroup extends ViewRecord
{
    protected static string $resource = CellGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CellGroupResource\Widgets\CellGroupPeopleTable::class,
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}

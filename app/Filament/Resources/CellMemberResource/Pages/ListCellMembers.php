<?php

namespace App\Filament\Resources\CellMemberResource\Pages;

use App\Filament\Resources\CellMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCellMembers extends ListRecords
{
    protected static string $resource = CellMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

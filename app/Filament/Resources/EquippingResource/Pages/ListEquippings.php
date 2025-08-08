<?php

namespace App\Filament\Resources\EquippingResource\Pages;

use App\Filament\Resources\EquippingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquippings extends ListRecords
{
    protected static string $resource = EquippingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

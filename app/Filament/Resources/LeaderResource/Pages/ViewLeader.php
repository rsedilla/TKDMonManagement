<?php

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLeader extends ViewRecord
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load equipping data into the form for viewing
        if ($this->record->equipping) {
            $data['training_attended'] = $this->record->equipping->training_attended;
            $data['have_cell_group'] = $this->record->equipping->have_cell_group;
        }

        return $data;
    }
}
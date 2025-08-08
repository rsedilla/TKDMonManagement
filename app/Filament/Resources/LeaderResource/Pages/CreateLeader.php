<?php

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLeader extends CreateRecord
{
    protected static string $resource = LeaderResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $formData = $this->form->getState();

        // Create or update the equipping record
        $record->equipping()->updateOrCreate(
            [],
            [
                'training_attended' => $formData['training_attended'] ?? null,
                'have_cell_group' => $formData['have_cell_group'] ?? false,
            ]
        );
    }
}

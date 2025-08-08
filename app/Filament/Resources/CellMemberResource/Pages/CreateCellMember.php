<?php

namespace App\Filament\Resources\CellMemberResource\Pages;

use App\Filament\Resources\CellMemberResource;
use App\Models\Equipping;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCellMember extends CreateRecord
{
    protected static string $resource = CellMemberResource::class;

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

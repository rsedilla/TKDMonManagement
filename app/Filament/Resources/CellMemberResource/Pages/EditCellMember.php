<?php

namespace App\Filament\Resources\CellMemberResource\Pages;

use App\Filament\Resources\CellMemberResource;
use App\Models\Equipping;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCellMember extends EditRecord
{
    protected static string $resource = CellMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load equipping data into the form
        if ($this->record->equipping) {
            $data['training_attended'] = $this->record->equipping->training_attended;
            $data['have_cell_group'] = $this->record->equipping->have_cell_group;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();
        
        // Create or update equipping record
        if (isset($data['training_attended']) || isset($data['have_cell_group'])) {
            $this->record->equipping()->updateOrCreate(
                [
                    'equippable_type' => 'App\\Models\\CellMember',
                    'equippable_id' => $this->record->id,
                ],
                [
                    'training_attended' => $data['training_attended'] ?? null,
                    'have_cell_group' => $data['have_cell_group'] ?? false,
                ]
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        // Redirect to view page after saving
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}

<?php

namespace App\Filament\Resources\ConsolidationResource\Pages;

use App\Filament\Resources\ConsolidationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsolidation extends EditRecord
{
    protected static string $resource = ConsolidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-populate the consolidator_selection field for editing
        if (isset($data['consolidator_type']) && isset($data['consolidator_id'])) {
            $data['consolidator_selection'] = $data['consolidator_type'] . ':' . $data['consolidator_id'];
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Redirect to view page after saving
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}

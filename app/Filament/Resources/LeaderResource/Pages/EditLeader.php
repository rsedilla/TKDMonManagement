<?php

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class EditLeader extends EditRecord
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_hierarchy')
                ->label('View Team Hierarchy')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalHeading('Team Hierarchy')
                ->modalContent(view('filament.components.leader-hierarchy', [
                    'leader' => $this->record
                ]))
                ->modalWidth('7xl'),
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

    protected function getRedirectUrl(): string
    {
        // Redirect to view page after saving
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}

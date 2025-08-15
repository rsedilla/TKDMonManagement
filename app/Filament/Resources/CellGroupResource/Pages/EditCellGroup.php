<?php

namespace App\Filament\Resources\CellGroupResource\Pages;

use App\Filament\Resources\CellGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCellGroup extends EditRecord
{
    protected static string $resource = CellGroupResource::class;

    public bool $isEditing = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->color('primary')
                ->visible(fn () => !$this->isEditing)
                ->action(function () {
                    $this->isEditing = true;
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // After saving, disable editing mode
        $this->isEditing = false;
        
        // Show success notification using Filament's notification system
        \Filament\Notifications\Notification::make()
            ->title('Cell Group updated successfully')
            ->body('Click Edit to make changes.')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // When loading the form, start in view mode
        $this->isEditing = false;
        return $data;
    }
}

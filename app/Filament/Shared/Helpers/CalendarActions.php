<?php

namespace App\Filament\Shared\Helpers;

use Filament\Forms\Components\Actions\Action;

class CalendarActions
{
    /**
     * Get Today button action for date picker fields
     * 
     * @param string $fieldName The name of the date field
     * @return Action
     */
    public static function todayAction(string $fieldName): Action
    {
        return Action::make('today')
            ->icon('heroicon-o-calendar')
            ->tooltip('Set to today')
            ->disabled(function (string $operation, $livewire = null) {
                return self::shouldDisableAction($operation, $livewire);
            })
            ->action(function (callable $set) use ($fieldName) {
                $set($fieldName, now()->format('Y-m-d'));
            });
    }

    /**
     * Get Clear button action for date picker fields
     * 
     * @param string $fieldName The name of the date field
     * @return Action
     */
    public static function clearAction(string $fieldName): Action
    {
        return Action::make('clear')
            ->icon('heroicon-o-x-mark')
            ->tooltip('Clear date')
            ->disabled(function (string $operation, $livewire = null) {
                return self::shouldDisableAction($operation, $livewire);
            })
            ->action(function (callable $set) use ($fieldName) {
                $set($fieldName, null);
            });
    }

    /**
     * Get both Today and Clear actions for a date picker field
     * 
     * @param string $fieldName The name of the date field
     * @return array
     */
    public static function getCalendarActions(string $fieldName): array
    {
        return [
            self::todayAction($fieldName),
            self::clearAction($fieldName),
        ];
    }

    /**
     * Determine if calendar actions should be disabled based on operation and edit state
     * 
     * @param string $operation The current form operation (view, edit, create)
     * @param mixed $livewire The livewire component instance
     * @return bool
     */
    private static function shouldDisableAction(string $operation, $livewire = null): bool
    {
        // Always disabled in view mode
        if ($operation === 'view') {
            return true;
        }
        
        // In edit mode, check if editing is enabled
        if ($operation === 'edit' && $livewire && property_exists($livewire, 'isEditing')) {
            return !$livewire->isEditing;
        }
        
        // Default: enabled for create mode
        return false;
    }
}

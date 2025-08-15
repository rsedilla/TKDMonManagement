<?php

namespace App\Filament\Shared\Helpers;

class FormHelpers
{
    /**
     * Standard disable logic for view-only operations
     * 
     * @return \Closure
     */
    public static function getDisabledClosure(): \Closure
    {
        return fn (string $operation) => $operation === 'view';
    }

    /**
     * Advanced disable logic with edit state checking
     * Used for forms that need to check if editing is enabled
     * 
     * @return \Closure
     */
    public static function getAdvancedDisabledClosure(): \Closure
    {
        return function (string $operation, $livewire = null) {
            if ($operation === 'view') {
                return true;
            }
            
            // Check if we're in edit mode and the page has isEditing property
            if ($operation === 'edit' && $livewire && property_exists($livewire, 'isEditing')) {
                return !$livewire->isEditing;
            }
            
            return false;
        };
    }

    /**
     * Helper to get consistent form field attributes
     * 
     * @param array $options
     * @return array
     */
    public static function getStandardFieldOptions(array $options = []): array
    {
        $defaults = [
            'required' => false,
            'maxLength' => 255,
            'disabled' => self::getDisabledClosure(),
        ];

        return array_merge($defaults, $options);
    }
}

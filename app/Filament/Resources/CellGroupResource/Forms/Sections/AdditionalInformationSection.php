<?php

namespace App\Filament\Resources\CellGroupResource\Forms\Sections;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class AdditionalInformationSection
{
    public static function get(): Section
    {
        return Forms\Components\Section::make('Additional Information')
            ->schema([
                self::getDescriptionField(),
                self::getIsActiveField(),
            ])
            ->columns(1);
    }

    private static function getDescriptionField()
    {
        return Textarea::make('description')
            ->label('Description')
            ->maxLength(500)
            ->disabled(self::isDisabled())
            ->rows(3);
    }

    private static function getIsActiveField()
    {
        return Toggle::make('is_active')
            ->label('Active')
            ->disabled(self::isDisabled())
            ->default(true);
    }

    private static function isDisabled(): \Closure
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
}

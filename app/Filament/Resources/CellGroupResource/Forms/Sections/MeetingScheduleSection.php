<?php

namespace App\Filament\Resources\CellGroupResource\Forms\Sections;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;

class MeetingScheduleSection
{
    public static function get(): Section
    {
        return Forms\Components\Section::make('Meeting Schedule')
            ->schema([
                self::getMeetingDayField(),
                self::getMeetingTimeField(),
                self::getMeetingLocationField(),
            ])
            ->columns(3);
    }

    private static function getMeetingDayField()
    {
        return Select::make('meeting_day')
            ->label('Meeting Day')
            ->options([
                'Monday' => 'Monday',
                'Tuesday' => 'Tuesday',
                'Wednesday' => 'Wednesday',
                'Thursday' => 'Thursday',
                'Friday' => 'Friday',
                'Saturday' => 'Saturday',
                'Sunday' => 'Sunday',
            ])
            ->required()
            ->disabled(self::isDisabled());
    }

    private static function getMeetingTimeField()
    {
        return TimePicker::make('meeting_time')
            ->label('Meeting Time')
            ->required()
            ->disabled(self::isDisabled())
            ->seconds(false);
    }

    private static function getMeetingLocationField()
    {
        return TextInput::make('meeting_location')
            ->label('Meeting Location')
            ->required()
            ->disabled(self::isDisabled())
            ->maxLength(255);
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

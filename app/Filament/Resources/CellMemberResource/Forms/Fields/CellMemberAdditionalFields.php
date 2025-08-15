<?php

namespace App\Filament\Resources\CellMemberResource\Forms\Fields;

use App\Filament\Shared\Fields\CommonStatusFields;
use App\Filament\Shared\Helpers\CalendarActions;
use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;

class CellMemberAdditionalFields
{
    public static function trainingAttendedField()
    {
        return CommonStatusFields::trainingLevelField([
            'label' => 'Training Attended (SOL = School of Leaders)',
            'fieldName' => 'training_attended'
        ]);
    }

    public static function haveCellGroupField(): Toggle
    {
        return Toggle::make('have_cell_group')
            ->label('Have Cell Group?')
            ->default(false)
            ->helperText('Toggle on if this member has their own cell group')
            ->afterStateHydrated(function ($component, $record) {
                if ($record && $record->equipping) {
                    $component->state((bool) $record->equipping->have_cell_group);
                } else {
                    $component->state(false);
                }
            })
            ->disabled(FormHelpers::getDisabledClosure());
    }

    public static function statusField(): Toggle
    {
        return CommonStatusFields::statusField([
            'label' => 'Active Status',
            'helperText' => 'Toggle to activate/deactivate the cell member'
        ]);
    }

    public static function notesField(): Textarea
    {
        return Textarea::make('notes')
            ->label('Additional Notes')
            ->placeholder('Any additional information about the cell member...')
            ->rows(3)
            ->columnSpanFull()
            ->disabled(FormHelpers::getDisabledClosure());
    }

    public static function enrollmentDateField(): DatePicker
    {
        return DatePicker::make('enrollment_date')
            ->label('Join Date')
            ->default(now())
            ->maxDate(now())
            ->displayFormat('M d, Y')
            ->format('Y-m-d')
            ->closeOnDateSelection()
            ->native(false)
            ->suffixActions(CalendarActions::getCalendarActions('enrollment_date'))
            ->disabled(FormHelpers::getDisabledClosure());
    }
}

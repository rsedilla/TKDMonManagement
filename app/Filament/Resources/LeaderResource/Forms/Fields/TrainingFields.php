<?php

namespace App\Filament\Resources\LeaderResource\Forms\Fields;

use App\Filament\Shared\Fields\CommonStatusFields;
use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class TrainingFields
{
    public static function trainingAttendedField(): Select
    {
        return CommonStatusFields::trainingLevelField([
            'label' => 'Training Attended (SOL = School of Leaders)'
        ]);
    }

    public static function haveCellGroupField(): Toggle
    {
        return Toggle::make('have_cell_group')
            ->label('Has Cell Group')
            ->helperText('Does this leader have their own cell group?')
            ->default(false)
            ->disabled(FormHelpers::getDisabledClosure());
    }
}

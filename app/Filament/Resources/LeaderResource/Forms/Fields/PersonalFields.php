<?php

namespace App\Filament\Resources\LeaderResource\Forms\Fields;

use App\Filament\Shared\Fields\CommonPersonalFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;

class PersonalFields
{
    public static function nameField(): TextInput
    {
        return CommonPersonalFields::nameField([
            'placeholder' => 'Enter cell leader full name',
            'helperText' => 'Please enter the complete leader name'
        ]);
    }

    public static function ageAndBirthdayGrid(): Grid
    {
        return CommonPersonalFields::ageAndBirthdayGrid();
    }

    public static function emailAndPhoneGrid(): Grid
    {
        return CommonPersonalFields::emailAndPhoneGrid();
    }
}

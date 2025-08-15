<?php

namespace App\Filament\Resources\CellMemberResource\Forms\Fields;

use App\Filament\Shared\Fields\CommonPersonalFields;
use App\Filament\Shared\Fields\CommonStatusFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;

class CellMemberPersonalFields
{
    public static function nameField(): TextInput
    {
        return CommonPersonalFields::nameField([
            'placeholder' => 'Enter cell member full name',
            'helperText' => 'Please enter the complete member name'
        ]);
    }

    public static function ageAndBirthdayGrid(): Grid
    {
        return CommonPersonalFields::ageAndBirthdayGrid([
            'birthday' => [
                'label' => 'Birthday',
                'helperText' => 'Age is automatically calculated from birthday'
            ]
        ]);
    }

    public static function networkAndCivilStatusGrid(): Grid
    {
        return Grid::make(2)
            ->schema([
                CommonStatusFields::networkField([
                    'required' => true,
                    'options' => [
                        'mens' => 'Mens',
                        'womens' => 'Womens',
                    ]
                ]),
                CommonStatusFields::civilStatusField([
                    'options' => [
                        'single' => 'Single',
                        'married' => 'Married',
                        'widow' => 'Widow',
                    ]
                ]),
            ]);
    }
}

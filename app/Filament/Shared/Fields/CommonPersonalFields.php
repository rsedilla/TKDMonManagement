<?php

namespace App\Filament\Shared\Fields;

use App\Filament\Shared\Helpers\CalendarActions;
use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;

class CommonPersonalFields
{
    /**
     * Reusable name field with customizable options
     */
    public static function nameField(array $options = []): TextInput
    {
        $defaults = [
            'required' => true,
            'maxLength' => 255,
            'placeholder' => 'Enter full name',
            'helperText' => 'Please enter the complete name',
            'prefixIcon' => 'heroicon-o-user',
            'label' => 'Name'
        ];

        $config = array_merge($defaults, $options);

        return TextInput::make('name')
            ->label($config['label'])
            ->required($config['required'])
            ->maxLength($config['maxLength'])
            ->placeholder($config['placeholder'])
            ->helperText($config['helperText'])
            ->prefixIcon($config['prefixIcon'])
            ->disabled(FormHelpers::getDisabledClosure());
    }

    /**
     * Reusable email field with customizable options
     */
    public static function emailField(array $options = []): TextInput
    {
        $defaults = [
            'required' => true,
            'maxLength' => 255,
            'placeholder' => 'Enter email address',
            'prefixIcon' => 'heroicon-o-envelope',
            'label' => 'Email'
        ];

        $config = array_merge($defaults, $options);

        return TextInput::make('email')
            ->label($config['label'])
            ->email()
            ->required($config['required'])
            ->maxLength($config['maxLength'])
            ->placeholder($config['placeholder'])
            ->prefixIcon($config['prefixIcon'])
            ->disabled(FormHelpers::getDisabledClosure());
    }

    /**
     * Reusable phone field with customizable options
     */
    public static function phoneField(array $options = []): TextInput
    {
        $defaults = [
            'required' => false,
            'maxLength' => 255,
            'placeholder' => 'Enter phone number',
            'prefixIcon' => 'heroicon-o-phone',
            'label' => 'Phone'
        ];

        $config = array_merge($defaults, $options);

        return TextInput::make('phone')
            ->label($config['label'])
            ->tel()
            ->required($config['required'])
            ->maxLength($config['maxLength'])
            ->placeholder($config['placeholder'])
            ->prefixIcon($config['prefixIcon'])
            ->disabled(FormHelpers::getDisabledClosure());
    }

    /**
     * Reusable age field with auto-calculation from birthday
     */
    public static function ageField(array $options = []): TextInput
    {
        $defaults = [
            'placeholder' => 'Auto-calculated from birthday',
            'suffix' => 'years old',
            'helperText' => 'Age will be calculated from birthday if provided',
            'label' => 'Age'
        ];

        $config = array_merge($defaults, $options);

        return TextInput::make('age')
            ->label($config['label'])
            ->numeric()
            ->minValue(1)
            ->maxValue(120)
            ->placeholder($config['placeholder'])
            ->suffix($config['suffix'])
            ->helperText($config['helperText'])
            ->disabled(FormHelpers::getDisabledClosure());
    }

    /**
     * Reusable birthday field with auto age calculation
     */
    public static function birthdayField(array $options = []): DatePicker
    {
        $defaults = [
            'placeholder' => 'Select birth date',
            'helperText' => 'Age will be automatically calculated',
            'calculateAge' => true,
            'label' => 'Birthday'
        ];

        $config = array_merge($defaults, $options);

        $field = DatePicker::make('birthday')
            ->label($config['label'])
            ->placeholder($config['placeholder'])
            ->maxDate(now())
            ->minDate(now()->subYears(120))
            ->displayFormat('M d, Y')
            ->format('Y-m-d')
            ->closeOnDateSelection()
            ->native(false)
            ->helperText($config['helperText'])
            ->suffixActions(CalendarActions::getCalendarActions('birthday'))
            ->disabled(FormHelpers::getDisabledClosure());

        if ($config['calculateAge']) {
            $field->live()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $age = \Carbon\Carbon::parse($state)->age;
                        $set('age', $age);
                    }
                });
        }

        return $field;
    }

    /**
     * Reusable age and birthday grid
     */
    public static function ageAndBirthdayGrid(array $options = []): Grid
    {
        return Grid::make(2)
            ->schema([
                self::ageField($options['age'] ?? []),
                self::birthdayField($options['birthday'] ?? []),
            ]);
    }

    /**
     * Reusable email and phone grid
     */
    public static function emailAndPhoneGrid(array $options = []): Grid
    {
        return Grid::make(2)
            ->schema([
                self::emailField($options['email'] ?? []),
                self::phoneField($options['phone'] ?? []),
            ]);
    }
}

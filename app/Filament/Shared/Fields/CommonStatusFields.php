<?php

namespace App\Filament\Shared\Fields;

use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class CommonStatusFields
{
    /**
     * Reusable status/active toggle field
     */
    public static function statusField(array $options = []): Toggle
    {
        $defaults = [
            'label' => 'Active',
            'helperText' => 'Whether this record is currently active',
            'default' => true,
            'fieldName' => 'status'
        ];

        $config = array_merge($defaults, $options);

        return Toggle::make($config['fieldName'])
            ->label($config['label'])
            ->default($config['default'])
            ->helperText($config['helperText'])
            ->disabled(FormHelpers::getDisabledClosure());
    }

    /**
     * Reusable civil status field
     */
    public static function civilStatusField(array $options = []): Select
    {
        $defaults = [
            'placeholder' => 'Select civil status',
            'options' => [
                'single' => 'Single',
                'married' => 'Married',
                'widowed' => 'Widowed',
                'divorced' => 'Divorced',
            ],
            'label' => 'Civil Status'
        ];

        $config = array_merge($defaults, $options);

        return Select::make('civil_status')
            ->label($config['label'])
            ->options($config['options'])
            ->placeholder($config['placeholder'])
            ->native(false)
            ->disabled(FormHelpers::getDisabledClosure());
    }

    /**
     * Reusable network selection field
     */
    public static function networkField(array $options = []): Select
    {
        $defaults = [
            'placeholder' => 'Select network',
            'options' => [
                'mens' => 'Men\'s Network',
                'womens' => 'Women\'s Network',
            ],
            'label' => 'Network',
            'required' => false
        ];

        $config = array_merge($defaults, $options);

        return Select::make('network')
            ->label($config['label'])
            ->options($config['options'])
            ->placeholder($config['placeholder'])
            ->required($config['required'])
            ->native(false)
            ->disabled(FormHelpers::getDisabledClosure());
    }

    /**
     * Reusable training level field
     */
    public static function trainingLevelField(array $options = []): Select
    {
        $defaults = [
            'placeholder' => 'Select training level',
            'helperText' => 'Choose the highest level completed',
            'options' => [
                'SUYNL' => 'SUYNL',
                'LIFECLASS' => 'LIFECLASS',
                'ENCOUNTER' => 'ENCOUNTER',
                'SOL1' => 'SOL 1',
                'SOL2' => 'SOL 2',
                'SOL3' => 'SOL 3',
                'SOL GRADUATE' => 'SOL GRADUATE',
            ],
            'label' => 'Training Attended',
            'fieldName' => 'training_attended'
        ];

        $config = array_merge($defaults, $options);

        return Select::make($config['fieldName'])
            ->label($config['label'])
            ->options($config['options'])
            ->placeholder($config['placeholder'])
            ->helperText($config['helperText'])
            ->reactive()
            ->disabled(FormHelpers::getDisabledClosure());
    }
}

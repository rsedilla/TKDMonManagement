<?php

namespace App\Filament\Shared\Examples;

use App\Filament\Shared\Fields\CommonPersonalFields;
use App\Filament\Shared\Fields\CommonStatusFields;
use Filament\Forms\Components\Section;

/**
 * Example demonstrating how to reuse shared field components
 * across different forms in the application
 */
class ReusableFormExample
{
    /**
     * Example: Creating a complete person form using shared components
     */
    public static function createPersonForm(): array
    {
        return [
            Section::make('Personal Information')
                ->schema([
                    // Reusing shared name field with custom placeholder
                    CommonPersonalFields::nameField([
                        'placeholder' => 'Enter person full name',
                        'helperText' => 'Name will be used across the system'
                    ]),
                    
                    // Reusing shared age and birthday grid
                    CommonPersonalFields::ageAndBirthdayGrid(),
                    
                    // Reusing shared email and phone grid
                    CommonPersonalFields::emailAndPhoneGrid([
                        'email' => ['required' => false], // Override email to be optional
                        'phone' => ['required' => true]   // Override phone to be required
                    ]),
                ])
                ->columns(1),

            Section::make('Status Information')
                ->schema([
                    // Reusing shared civil status field
                    CommonStatusFields::civilStatusField(),
                    
                    // Reusing shared network field with custom options
                    CommonStatusFields::networkField([
                        'options' => [
                            'mens' => 'Men\'s Ministry',
                            'womens' => 'Women\'s Ministry',
                            'youth' => 'Youth Ministry',
                            'children' => 'Children\'s Ministry'
                        ]
                    ]),
                    
                    // Reusing shared training level field
                    CommonStatusFields::trainingLevelField(),
                    
                    // Reusing shared status field with custom label
                    CommonStatusFields::statusField([
                        'label' => 'Is Active Member',
                        'helperText' => 'Active members receive communications'
                    ]),
                ])
                ->columns(2),
        ];
    }

    /**
     * Example: Creating a minimal contact form using shared components
     */
    public static function createContactForm(): array
    {
        return [
            CommonPersonalFields::nameField([
                'placeholder' => 'Your full name',
                'helperText' => 'How should we address you?'
            ]),
            
            CommonPersonalFields::emailField([
                'required' => true,
                'placeholder' => 'your.email@example.com'
            ]),
            
            CommonPersonalFields::phoneField([
                'required' => false,
                'placeholder' => 'Optional phone number'
            ]),
        ];
    }

    /**
     * Example: Creating a registration form using shared components
     */
    public static function createRegistrationForm(): array
    {
        return [
            Section::make('Registration Details')
                ->schema([
                    CommonPersonalFields::nameField([
                        'placeholder' => 'Enter your full name for registration'
                    ]),
                    
                    CommonPersonalFields::emailAndPhoneGrid([
                        'email' => ['required' => true],
                        'phone' => ['required' => true]
                    ]),
                    
                    CommonPersonalFields::birthdayField([
                        'helperText' => 'Required for age verification'
                    ]),
                    
                    CommonStatusFields::networkField([
                        'required' => true,
                        'label' => 'Ministry Interest'
                    ]),
                ])
                ->columns(1),
        ];
    }
}

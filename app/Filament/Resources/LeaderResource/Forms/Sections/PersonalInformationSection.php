<?php

namespace App\Filament\Resources\LeaderResource\Forms\Sections;

use App\Filament\Resources\LeaderResource\Forms\Fields\PersonalFields;
use Filament\Forms\Components\Section;

class PersonalInformationSection
{
    public static function make(): Section
    {
        return Section::make('Personal Information')
            ->description('Enter the basic personal details')
            ->icon('heroicon-o-user')
            ->collapsible()
            ->schema([
                PersonalFields::nameField(),
                PersonalFields::ageAndBirthdayGrid(),
                PersonalFields::emailAndPhoneGrid(),
            ])
            ->columns(1);
    }
}

<?php

namespace App\Filament\Resources\LeaderResource\Forms\Sections;

use App\Filament\Resources\LeaderResource\Forms\Fields\LeadershipFields;
use Filament\Forms\Components\Section;

class LeadershipInformationSection
{
    public static function make(): Section
    {
        return Section::make('Leadership Information')
            ->description('Organizational details and network assignment')
            ->icon('heroicon-o-briefcase')
            ->collapsible()
            ->schema([
                LeadershipFields::networkGrid(),
                LeadershipFields::civilStatusField(),
                LeadershipFields::parentLeaderField(),
                LeadershipFields::cellGroupField(),
            ])
            ->columns(2);
    }
}

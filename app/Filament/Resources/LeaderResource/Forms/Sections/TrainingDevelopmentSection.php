<?php

namespace App\Filament\Resources\LeaderResource\Forms\Sections;

use App\Filament\Resources\LeaderResource\Forms\Fields\TrainingFields;
use Filament\Forms\Components\Section;

class TrainingDevelopmentSection
{
    public static function make(): Section
    {
        return Section::make('Training & Development')
            ->description('Equipping and leadership development information')
            ->icon('heroicon-o-academic-cap')
            ->schema([
                TrainingFields::trainingAttendedField(),
                TrainingFields::haveCellGroupField(),
            ])
            ->columns(2);
    }
}

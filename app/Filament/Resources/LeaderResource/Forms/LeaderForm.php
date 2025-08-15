<?php

namespace App\Filament\Resources\LeaderResource\Forms;

use App\Filament\Resources\LeaderResource\Forms\Sections\PersonalInformationSection;
use App\Filament\Resources\LeaderResource\Forms\Sections\LeadershipInformationSection;
use App\Filament\Resources\LeaderResource\Forms\Sections\TrainingDevelopmentSection;
use App\Filament\Resources\LeaderResource\Forms\Sections\AdditionalInformationSection;

class LeaderForm
{
    /**
     * Get complete form schema
     */
    public static function schema(): array
    {
        return [
            PersonalInformationSection::make(),
            LeadershipInformationSection::make(),
            TrainingDevelopmentSection::make(),
            AdditionalInformationSection::make(),
        ];
    }
}

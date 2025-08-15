<?php

namespace App\Filament\Resources\CellMemberResource\Forms;

use Filament\Forms\Form;
use App\Filament\Resources\CellMemberResource\Forms\Sections\PersonalInformationSection;
use App\Filament\Resources\CellMemberResource\Forms\Sections\TrainingDevelopmentSection;
use App\Filament\Resources\CellMemberResource\Forms\Sections\AdditionalInformationSection;

class FormConfiguration
{
    public static function get(): array
    {
        return [
            PersonalInformationSection::get(),
            TrainingDevelopmentSection::get(),
            AdditionalInformationSection::get(),
        ];
    }
}

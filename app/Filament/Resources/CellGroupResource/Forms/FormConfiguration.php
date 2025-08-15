<?php

namespace App\Filament\Resources\CellGroupResource\Forms;

use App\Filament\Resources\CellGroupResource\Forms\Sections\CellGroupInformationSection;
use App\Filament\Resources\CellGroupResource\Forms\Sections\MeetingScheduleSection;
use App\Filament\Resources\CellGroupResource\Forms\Sections\AdditionalInformationSection;

class FormConfiguration
{
    public static function get(): array
    {
        return [
            CellGroupInformationSection::get(),
            MeetingScheduleSection::get(),
            AdditionalInformationSection::get(),
        ];
    }
}

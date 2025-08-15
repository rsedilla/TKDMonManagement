<?php

namespace App\Filament\Resources\ConsolidationResource\Forms;

use App\Filament\Resources\ConsolidationResource\Forms\Sections\VipInformationSection;
use App\Filament\Resources\ConsolidationResource\Forms\Sections\ConsolidationDetailsSection;
use App\Filament\Resources\ConsolidationResource\Forms\Sections\VipProgressTrackingSection;

class ConsolidationForm
{
    /**
     * Get the complete form schema
     */
    public static function getSchema(): array
    {
        return [
            VipInformationSection::get(),
            ConsolidationDetailsSection::get(),
            VipProgressTrackingSection::get(),
        ];
    }
}

<?php

namespace App\Filament\Resources\LeaderResource\Forms\Sections;

use App\Filament\Resources\LeaderResource\Forms\Fields\AdditionalFields;
use Filament\Forms\Components\Section;

class AdditionalInformationSection
{
    public static function make(): Section
    {
        return Section::make('Additional Information')
            ->description('Bio and status information')
            ->icon('heroicon-o-document-text')
            ->collapsible()
            ->schema([
                AdditionalFields::bioField(),
                AdditionalFields::statusField(),
            ])
            ->columns(1);
    }
}

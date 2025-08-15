<?php

namespace App\Filament\Resources\ConsolidationResource\Forms\Sections;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\Consolidation;
use App\Filament\Shared\Helpers\FormHelpers;
use App\Filament\Shared\Factories\SectionFactory;

class VipInformationSection
{
    public static function get(): Forms\Components\Section
    {
        return SectionFactory::vipInformation()
            ->schema([
                self::getVipNameField(),
                self::getVipContactDetailsField(),
                self::getVipAddressField(),
                self::getVipStatusField(),
            ]);
    }

    private static function getVipNameField(): TextInput
    {
        return TextInput::make('vip_name')
            ->label('VIP Name')
            ->required()
            ->maxLength(255)
            ->placeholder('Enter VIP full name')
            ->prefixIcon('heroicon-o-user')
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function getVipContactDetailsField(): Textarea
    {
        return Textarea::make('vip_contact_details')
            ->label('VIP Contact Details')
            ->required()
            ->placeholder('Enter phone number, email, etc.')
            ->rows(3)
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function getVipAddressField(): Textarea
    {
        return Textarea::make('vip_address')
            ->label('VIP Address')
            ->required()
            ->placeholder('Enter complete address')
            ->rows(3)
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function getVipStatusField(): Select
    {
        return Select::make('vip_status')
            ->label('VIP Status')
            ->options(Consolidation::getVipStatusOptions())
            ->required()
            ->native(false)
            ->disabled(FormHelpers::getDisabledClosure());
    }
}

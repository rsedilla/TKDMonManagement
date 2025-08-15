<?php

namespace App\Filament\Resources\LeaderResource\Forms\Fields;

use App\Filament\Shared\Fields\CommonStatusFields;
use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class AdditionalFields
{
    public static function bioField(): Textarea
    {
        return Textarea::make('bio')
            ->maxLength(500)
            ->rows(3)
            ->placeholder('Enter a brief biography or description')
            ->disabled(FormHelpers::getDisabledClosure());
    }

    public static function statusField(): Toggle
    {
        return CommonStatusFields::statusField([
            'helperText' => 'Whether this leader is currently active'
        ]);
    }
}

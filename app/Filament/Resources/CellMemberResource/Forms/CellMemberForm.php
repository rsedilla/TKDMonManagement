<?php

namespace App\Filament\Resources\CellMemberResource\Forms;

use App\Filament\Resources\CellMemberResource\Forms\FormConfiguration;

class CellMemberForm
{
    public static function schema(): array
    {
        return FormConfiguration::get();
    }
}
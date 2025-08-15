<?php

namespace App\Filament\Resources\CellGroupResource\Forms;

use App\Filament\Resources\CellGroupResource\Forms\FormConfiguration;

class CellGroupForm
{
    public static function getSchema(): array
    {
        return FormConfiguration::get();
    }
}
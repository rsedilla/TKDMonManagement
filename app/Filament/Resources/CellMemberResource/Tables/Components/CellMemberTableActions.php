<?php

namespace App\Filament\Resources\CellMemberResource\Tables\Components;

use Filament\Tables;

class CellMemberTableActions
{
    public static function get(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ];
    }
}

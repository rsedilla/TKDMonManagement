<?php

namespace App\Filament\Resources\ConsolidationResource\Tables\Actions;

use Filament\Tables;

class ConsolidationTableActions
{
    public static function get(): array
    {
        return [
            self::getViewAction(),
            self::getEditAction(),
            self::getDeleteAction(),
        ];
    }

    private static function getViewAction(): Tables\Actions\ViewAction
    {
        return Tables\Actions\ViewAction::make();
    }

    private static function getEditAction(): Tables\Actions\EditAction
    {
        return Tables\Actions\EditAction::make();
    }

    private static function getDeleteAction(): Tables\Actions\DeleteAction
    {
        return Tables\Actions\DeleteAction::make();
    }
}

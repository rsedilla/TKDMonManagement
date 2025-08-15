<?php

namespace App\Filament\Resources\ConsolidationResource\Tables\BulkActions;

use Filament\Tables;

class ConsolidationTableBulkActions
{
    public static function get(): array
    {
        return [
            self::getBulkActionGroup(),
        ];
    }

    private static function getBulkActionGroup(): Tables\Actions\BulkActionGroup
    {
        return Tables\Actions\BulkActionGroup::make([
            self::getDeleteBulkAction(),
        ]);
    }

    private static function getDeleteBulkAction(): Tables\Actions\DeleteBulkAction
    {
        return Tables\Actions\DeleteBulkAction::make();
    }
}

<?php

namespace App\Filament\Resources\LeaderResource\Tables\BulkActions;

use Filament\Tables;

class LeaderTableBulkActions
{
    /**
     * Get table bulk actions configuration
     */
    public static function get(): array
    {
        return [
            self::bulkActionGroup(),
        ];
    }

    private static function bulkActionGroup(): Tables\Actions\BulkActionGroup
    {
        return Tables\Actions\BulkActionGroup::make([
            self::deleteBulkAction(),
        ]);
    }

    private static function deleteBulkAction(): Tables\Actions\DeleteBulkAction
    {
        return Tables\Actions\DeleteBulkAction::make();
    }
}

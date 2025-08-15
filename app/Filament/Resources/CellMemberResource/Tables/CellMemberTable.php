<?php

namespace App\Filament\Resources\CellMemberResource\Tables;

use App\Filament\Resources\CellMemberResource\Tables\TableConfiguration;

class CellMemberTable
{
    /**
     * Get table columns configuration
     */
    public static function columns(): array
    {
        return TableConfiguration::getColumns();
    }

    /**
     * Get table filters configuration
     */
    public static function filters(): array
    {
        return TableConfiguration::getFilters();
    }

    /**
     * Get table actions configuration
     */
    public static function actions(): array
    {
        return TableConfiguration::getActions();
    }

    /**
     * Get table bulk actions configuration
     */
    public static function bulkActions(): array
    {
        return TableConfiguration::getBulkActions();
    }

    /**
     * Get default sort configuration
     */
    public static function defaultSort(): string
    {
        return TableConfiguration::getDefaultSort();
    }
}

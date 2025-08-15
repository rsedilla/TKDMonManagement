<?php

namespace App\Filament\Resources\LeaderResource\Tables;

use App\Filament\Resources\LeaderResource\Tables\Columns\LeaderTableColumns;
use App\Filament\Resources\LeaderResource\Tables\Filters\LeaderTableFilters;
use App\Filament\Resources\LeaderResource\Tables\Actions\LeaderTableActions;
use App\Filament\Resources\LeaderResource\Tables\BulkActions\LeaderTableBulkActions;
use App\Filament\Resources\LeaderResource\Tables\Configuration\LeaderTableConfiguration;

class LeaderTable
{
    /**
     * Get table columns configuration
     */
    public static function columns(): array
    {
        return LeaderTableColumns::get();
    }

    /**
     * Get table filters configuration
     */
    public static function filters(): array
    {
        return LeaderTableFilters::get();
    }

    /**
     * Get table actions configuration
     */
    public static function actions(): array
    {
        return LeaderTableActions::get();
    }

    /**
     * Get table bulk actions configuration
     */
    public static function bulkActions(): array
    {
        return LeaderTableBulkActions::get();
    }

    /**
     * Get default sort configuration
     */
    public static function defaultSort(): array
    {
        return LeaderTableConfiguration::defaultSort();
    }
}

<?php

namespace App\Filament\Resources\LeaderResource\Tables\Configuration;

class LeaderTableConfiguration
{
    /**
     * Get default sort configuration
     */
    public static function defaultSort(): array
    {
        return ['created_at', 'desc'];
    }

    /**
     * Get table pagination configuration
     */
    public static function paginationOptions(): array
    {
        return [10, 25, 50, 100];
    }

    /**
     * Get default records per page
     */
    public static function defaultRecordsPerPage(): int
    {
        return 25;
    }

    /**
     * Get table query optimization settings
     */
    public static function queryOptimizations(): array
    {
        return [
            'eager_load' => ['parentLeader', 'cellGroup', 'equipping'],
            'select_columns' => ['*'], // Can be optimized to specific columns if needed
        ];
    }
}

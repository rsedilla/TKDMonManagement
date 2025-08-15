<?php

namespace App\Filament\Resources\LeaderResource\Tables\Filters;

use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class LeaderTableFilters
{
    /**
     * Get table filters configuration
     */
    public static function get(): array
    {
        return [
            self::networkFilter(),
            self::parentLeaderFilter(),
            self::topLevelFilter(),
            self::statusFilter(),
            self::trainingLevelFilter(),
        ];
    }

    private static function networkFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('network')
            ->options([
                'mens' => 'Men\'s Network',
                'womens' => 'Women\'s Network',
            ]);
    }

    private static function parentLeaderFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('parent_leader_id')
            ->label('Reports To')
            ->relationship('parentLeader', 'name')
            ->searchable()
            ->preload();
    }

    private static function topLevelFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('top_level')
            ->label('Top Level Leaders')
            ->query(fn (Builder $query): Builder => $query->whereNull('parent_leader_id'))
            ->toggle();
    }

    private static function statusFilter(): Tables\Filters\TernaryFilter
    {
        return Tables\Filters\TernaryFilter::make('status')
            ->label('Active Status');
    }

    private static function trainingLevelFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('training_level')
            ->label('Training Level')
            ->options([
                'SUYNL' => 'SUYNL',
                'LIFECLASS' => 'LIFECLASS',
                'ENCOUNTER' => 'ENCOUNTER',
                'SOL1' => 'SOL 1',
                'SOL2' => 'SOL 2',
                'SOL3' => 'SOL 3',
                'SOL GRADUATE' => 'SOL GRADUATE',
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query->when(
                    $data['value'],
                    fn (Builder $query, $training) => $query->whereHas('equipping', 
                        fn (Builder $query) => $query->where('training_attended', $training)
                    )
                );
            });
    }
}

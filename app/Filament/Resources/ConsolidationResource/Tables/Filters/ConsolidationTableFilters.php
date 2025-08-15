<?php

namespace App\Filament\Resources\ConsolidationResource\Tables\Filters;

use App\Models\Consolidation;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ConsolidationTableFilters
{
    public static function get(): array
    {
        return [
            self::getConsolidationPlaceFilter(),
            self::getVipStatusFilter(),
            self::getConsolidatorTypeFilter(),
            self::getSuynlCompletedFilter(),
            self::getServicesCompletedFilter(),
            self::getCellGroupCompletedFilter(),
        ];
    }

    private static function getConsolidationPlaceFilter(): SelectFilter
    {
        return SelectFilter::make('consolidation_place')
            ->label('Place')
            ->options(Consolidation::getConsolidationPlaceOptions());
    }

    private static function getVipStatusFilter(): SelectFilter
    {
        return SelectFilter::make('vip_status')
            ->label('VIP Status')
            ->options(Consolidation::getVipStatusOptions());
    }

    private static function getConsolidatorTypeFilter(): SelectFilter
    {
        return SelectFilter::make('consolidator_type')
            ->label('Consolidator Type')
            ->options([
                'App\\Models\\Leader' => 'Leader',
                'App\\Models\\CellMember' => 'Cell Member',
            ]);
    }

    private static function getSuynlCompletedFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('suynl_completed')
            ->label('SUYNL Completed (8+ lessons)')
            ->query(fn (Builder $query): Builder => 
                $query->whereRaw('JSON_LENGTH(suynl_lessons_completed) >= 8')
            );
    }

    private static function getServicesCompletedFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('services_completed')
            ->label('All Services Attended')
            ->query(fn (Builder $query): Builder => 
                $query->whereRaw('JSON_LENGTH(sunday_services_attended) = 4')
            );
    }

    private static function getCellGroupCompletedFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('cellgroup_completed')
            ->label('All Cell Groups Attended')
            ->query(fn (Builder $query): Builder => 
                $query->whereRaw('JSON_LENGTH(cell_group_attended) = 4')
            );
    }
}

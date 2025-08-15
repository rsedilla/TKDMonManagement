<?php

namespace App\Filament\Resources\ConsolidationResource\Tables;

use App\Filament\Resources\ConsolidationResource\Tables\Columns\ConsolidationTableColumns;
use App\Filament\Resources\ConsolidationResource\Tables\Filters\ConsolidationTableFilters;
use App\Filament\Resources\ConsolidationResource\Tables\Actions\ConsolidationTableActions;
use App\Filament\Resources\ConsolidationResource\Tables\BulkActions\ConsolidationTableBulkActions;
use Filament\Tables\Table;

class ConsolidationTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->columns(ConsolidationTableColumns::get())
            ->filters(ConsolidationTableFilters::get())
            ->actions(ConsolidationTableActions::get())
            ->bulkActions(ConsolidationTableBulkActions::get())
            ->defaultSort('consolidation_date', 'desc');
    }
}

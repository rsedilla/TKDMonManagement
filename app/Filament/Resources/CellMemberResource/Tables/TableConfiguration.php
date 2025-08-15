<?php

namespace App\Filament\Resources\CellMemberResource\Tables;

use App\Filament\Resources\CellMemberResource\Tables\Components\CellMemberTableColumns;
use App\Filament\Resources\CellMemberResource\Tables\Components\CellMemberTableFilters;
use App\Filament\Resources\CellMemberResource\Tables\Components\CellMemberTableActions;
use App\Filament\Resources\CellMemberResource\Tables\Components\CellMemberTableBulkActions;

class TableConfiguration
{
    public static function getColumns(): array
    {
        return CellMemberTableColumns::get();
    }

    public static function getFilters(): array
    {
        return CellMemberTableFilters::get();
    }

    public static function getActions(): array
    {
        return CellMemberTableActions::get();
    }

    public static function getBulkActions(): array
    {
        return CellMemberTableBulkActions::get();
    }

    public static function getDefaultSort(): string
    {
        return 'name';
    }
}

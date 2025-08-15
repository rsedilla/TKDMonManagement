<?php

namespace App\Filament\Resources\CellGroupResource\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CellGroupResource\Widgets\Components\CellGroupPeopleTableQuery;
use App\Filament\Resources\CellGroupResource\Widgets\Components\CellGroupPeopleTableColumns;
use App\Filament\Resources\CellGroupResource\Widgets\Components\CellGroupPeopleTableFilters;
use App\Filament\Resources\CellGroupResource\Widgets\Components\CellGroupPeopleTableDescription;

class CellGroupPeopleTable extends BaseWidget
{
    public $record;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return CellGroupPeopleTableQuery::get($this->record);
    }

    protected function getTableColumns(): array
    {
        return CellGroupPeopleTableColumns::get();
    }

    protected function getTableFilters(): array
    {
        return CellGroupPeopleTableFilters::get();
    }

    protected function getTableHeading(): string
    {
        return CellGroupPeopleTableDescription::getHeading($this->record);
    }

    protected function getTableDescription(): string
    {
        return CellGroupPeopleTableDescription::get($this->record);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->heading($this->getTableHeading())
            ->description($this->getTableDescription())
            ->defaultSort('name')
            ->paginated(false); // Disable pagination to show all records
    }
}
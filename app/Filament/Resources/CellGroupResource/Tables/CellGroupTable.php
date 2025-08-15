<?php

namespace App\Filament\Resources\CellGroupResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class CellGroupTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->columns(self::getColumns())
            ->filters(self::getFilters())
            ->actions(self::getActions())
            ->bulkActions(self::getBulkActions())
            ->defaultSort('created_at', 'desc');
    }

    private static function getColumns(): array
    {
        return [
            self::getCellGroupIdColumn(),
            self::getLeaderNameColumn(),
            self::getCellGroupTypeColumn(),
            self::getMeetingDayColumn(),
            self::getMeetingTimeColumn(),
            self::getMeetingLocationColumn(),
            self::getTotalMembersColumn(),
            self::getIsActiveColumn(),
            self::getCreatedAtColumn(),
        ];
    }

    private static function getCellGroupIdColumn(): TextColumn
    {
        return TextColumn::make('cell_group_id')
            ->label('Cell Group ID')
            ->searchable()
            ->sortable();
    }

    private static function getLeaderNameColumn(): TextColumn
    {
        return TextColumn::make('leader.name')
            ->label('Cell Leader')
            ->searchable()
            ->sortable();
    }

    private static function getCellGroupTypeColumn(): BadgeColumn
    {
        return BadgeColumn::make('cell_group_type')
            ->label('Type')
            ->colors([
                'success' => 'Cell Group',
                'warning' => 'Open Cell',
                'info' => 'G12 Cell',
            ]);
    }

    private static function getMeetingDayColumn(): TextColumn
    {
        return TextColumn::make('meeting_day')
            ->label('Meeting Day')
            ->sortable();
    }

    private static function getMeetingTimeColumn(): TextColumn
    {
        return TextColumn::make('meeting_time')
            ->label('Meeting Time')
            ->time('g:i A')
            ->sortable();
    }

    private static function getMeetingLocationColumn(): TextColumn
    {
        return TextColumn::make('meeting_location')
            ->label('Location')
            ->searchable()
            ->limit(30);
    }

    private static function getTotalMembersColumn(): TextColumn
    {
        return TextColumn::make('total_members')
            ->label('Members')
            ->getStateUsing(fn ($record) => $record->getTotalMembersCount())
            ->badge()
            ->color('success')
            ->sortable();
    }

    private static function getIsActiveColumn(): BooleanColumn
    {
        return BooleanColumn::make('is_active')
            ->label('Active')
            ->sortable();
    }

    private static function getCreatedAtColumn(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label('Created')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function getFilters(): array
    {
        return [
            self::getCellGroupTypeFilter(),
            self::getMeetingDayFilter(),
            self::getIsActiveFilter(),
        ];
    }

    private static function getCellGroupTypeFilter(): SelectFilter
    {
        return SelectFilter::make('cell_group_type')
            ->label('Cell Group Type')
            ->options([
                'Cell Group' => 'Cell Group',
                'Open Cell' => 'Open Cell',
                'G12 Cell' => 'G12 Cell',
            ]);
    }

    private static function getMeetingDayFilter(): SelectFilter
    {
        return SelectFilter::make('meeting_day')
            ->label('Meeting Day')
            ->options([
                'Monday' => 'Monday',
                'Tuesday' => 'Tuesday',
                'Wednesday' => 'Wednesday',
                'Thursday' => 'Thursday',
                'Friday' => 'Friday',
                'Saturday' => 'Saturday',
                'Sunday' => 'Sunday',
            ]);
    }

    private static function getIsActiveFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_active')
            ->label('Active Status');
    }

    private static function getActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    private static function getBulkActions(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ];
    }
}

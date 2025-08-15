<?php

namespace App\Filament\Resources\LeaderResource\Tables\Columns;

use Filament\Tables;

class LeaderTableColumns
{
    /**
     * Get table columns configuration
     */
    public static function get(): array
    {
        return [
            self::nameColumn(),
            self::networkSizeColumn(),
            self::phoneColumn(),
            self::parentLeaderColumn(),
            self::networkColumn(),
            self::cellGroupColumn(),
            self::trainingLevelColumn(),
            self::statusColumn(),
            self::createdAtColumn(),
        ];
    }

    private static function nameColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('name')
            ->searchable()
            ->sortable();
    }

    private static function networkSizeColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('network_size')
            ->label('No. of People')
            ->getStateUsing(fn ($record) => $record->getNetworkSize())
            ->badge()
            ->color('success')
            ->sortable(false)
            ->tooltip('Total number of people under this leader (including all levels)');
    }

    private static function phoneColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('phone')
            ->searchable()
            ->copyable()
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function parentLeaderColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('parentLeader.name')
            ->label('Cell Leader')
            ->searchable()
            ->sortable()
            ->placeholder('Top Level')
            ->badge()
            ->color('secondary')
            ->toggleable();
    }

    private static function networkColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('network')
            ->searchable()
            ->sortable()
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'mens' => 'blue',
                'womens' => 'pink',
                default => 'gray',
            })
            ->formatStateUsing(fn (string $state): string => ucfirst($state))
            ->placeholder('Not specified')
            ->toggleable();
    }

    private static function cellGroupColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('cellGroup.cell_group_id')
            ->label('Cell Group')
            ->searchable()
            ->sortable()
            ->placeholder('No cell group')
            ->badge()
            ->color('info');
    }

    private static function trainingLevelColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('equipping.training_attended')
            ->label('Training Level')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'SUYNL' => 'gray',
                'LIFECLASS' => 'blue',
                'ENCOUNTER' => 'green',
                'SOL1' => 'yellow',
                'SOL2' => 'orange',
                'SOL3' => 'red',
                'SOL GRADUATE' => 'purple',
                default => 'secondary'
            })
            ->placeholder('No training')
            ->toggleable();
    }

    private static function statusColumn(): Tables\Columns\BooleanColumn
    {
        return Tables\Columns\BooleanColumn::make('status')
            ->label('Active')
            ->sortable();
    }

    private static function createdAtColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('created_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }
}

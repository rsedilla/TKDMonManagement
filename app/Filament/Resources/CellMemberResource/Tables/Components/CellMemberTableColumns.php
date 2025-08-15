<?php

namespace App\Filament\Resources\CellMemberResource\Tables\Components;

use Filament\Tables;

class CellMemberTableColumns
{
    public static function get(): array
    {
        return [
            self::nameColumn(),
            self::birthdayColumn(),
            self::networkColumn(),
            self::civilStatusColumn(),
            ...self::leaderColumns(),
            self::cellGroupColumn(),
            self::trainingColumn(),
            self::enrollmentDateColumn(),
            self::notesColumn(),
            self::statusColumn(),
            ...self::timestampColumns(),
        ];
    }

    private static function nameColumn()
    {
        return Tables\Columns\TextColumn::make('name')
            ->searchable()
            ->sortable()
            ->weight('medium');
    }

    private static function birthdayColumn()
    {
        return Tables\Columns\TextColumn::make('birthday')
            ->date('M d, Y')
            ->sortable()
            ->placeholder('Not specified')
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function networkColumn()
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
            ->placeholder('Not specified');
    }

    private static function civilStatusColumn()
    {
        return Tables\Columns\TextColumn::make('civil_status')
            ->label('Civil Status')
            ->searchable()
            ->sortable()
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'single' => 'info',
                'married' => 'success',
                'widow' => 'warning',
                default => 'gray',
            })
            ->formatStateUsing(fn (string $state): string => ucfirst($state))
            ->placeholder('Not specified');
    }

    private static function leaderColumns()
    {
        return [
            Tables\Columns\TextColumn::make('leader.name')
                ->label('Cell Leader')
                ->searchable()
                ->sortable()
                ->placeholder('No cell leader assigned')
                ->badge()
                ->color('success'),
            
            Tables\Columns\TextColumn::make('leader.position')
                ->label('Cell Leader Position')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->placeholder('N/A'),
        ];
    }

    private static function cellGroupColumn()
    {
        return Tables\Columns\TextColumn::make('cellGroup.cell_group_id')
            ->label('Cell Group')
            ->searchable()
            ->sortable()
            ->placeholder('No cell group')
            ->badge()
            ->color('info');
    }

    private static function trainingColumn()
    {
        return Tables\Columns\TextColumn::make('equipping.training_attended')
            ->label('Training Attended')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'SUYNL' => 'gray',
                'LIFECLASS' => 'blue',
                'ENCOUNTER' => 'green',
                'SOL1' => 'yellow',
                'SOL2' => 'orange',
                'SOL3' => 'red',
                'SOL GRADUATE' => 'purple',
                default => 'gray',
            })
            ->placeholder('Not specified');
    }

    private static function enrollmentDateColumn()
    {
        return Tables\Columns\TextColumn::make('enrollment_date')
            ->label('Join Date')
            ->date()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function notesColumn()
    {
        return Tables\Columns\TextColumn::make('notes')
            ->label('Notes')
            ->limit(50)
            ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                $state = $column->getState();
                if (strlen($state) <= 50) {
                    return null;
                }
                return $state;
            })
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function statusColumn()
    {
        return Tables\Columns\IconColumn::make('status')
            ->boolean()
            ->label('Active')
            ->trueIcon('heroicon-o-check-circle')
            ->falseIcon('heroicon-o-x-circle')
            ->trueColor('success')
            ->falseColor('danger');
    }

    private static function timestampColumns()
    {
        return [
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}

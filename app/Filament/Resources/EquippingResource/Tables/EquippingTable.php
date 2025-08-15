<?php

namespace App\Filament\Resources\EquippingResource\Tables;

use App\Models\Equipping;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class EquippingTable
{
    /**
     * Get table columns configuration
     */
    public static function columns(): array
    {
        return [
            self::nameColumn(),
            self::typeColumn(),
            self::reportsToColumn(),
            self::trainingLevelColumn(),
            self::hasCellGroupColumn(),
            self::createdAtColumn(),
        ];
    }

    private static function nameColumn(): TextColumn
    {
        return TextColumn::make('Name')
            ->getStateUsing(function (Equipping $record, $rowLoop): string {
                $name = $record->equippable?->name ?? 'N/A';
                return "{$name}";
            })
            ->searchable()
            ->sortable();
    }

    private static function typeColumn(): TextColumn
    {
        return TextColumn::make('Type')
            ->getStateUsing(function (Equipping $record): string {
                return $record->equippable_type === 'App\\Models\\Leader' ? 'Leader' : 'Cell Member';
            })
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'Leader' => 'success',
                'Cell Member' => 'info',
                default => 'gray',
            });
    }

    private static function reportsToColumn(): TextColumn
    {
        return TextColumn::make('Reports To')
            ->getStateUsing(function (Equipping $record): string {
                if ($record->equippable_type === 'App\\Models\\CellMember') {
                    return $record->equippable?->leader?->name ?? 'No Leader';
                } elseif ($record->equippable_type === 'App\\Models\\Leader') {
                    return $record->equippable?->parentLeader?->name ?? 'Top Level';
                }
                return 'N/A';
            });
    }

    private static function trainingLevelColumn(): BadgeColumn
    {
        return BadgeColumn::make('training_attended')
            ->label('Training Level')
            ->colors([
                'danger' => 'SUYNL',
                'warning' => 'LIFECLASS',
                'info' => 'ENCOUNTER',
                'success' => 'SOL1',
                'success' => 'SOL2',
                'success' => 'SOL3',
                'primary' => 'SOL GRADUATE',
            ]);
    }

    private static function hasCellGroupColumn()
    {
        return Tables\Columns\IconColumn::make('have_cell_group')
            ->label('Has Cell Group')
            ->boolean();
    }

    private static function createdAtColumn(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label('Date Added')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }

    /**
     * Get table filters configuration
     */
    public static function filters(): array
    {
        return [
            self::trainingLevelFilter(),
            self::personTypeFilter(),
            self::hasCellGroupFilter(),
        ];
    }

    private static function trainingLevelFilter(): SelectFilter
    {
        return SelectFilter::make('training_attended')
            ->label('Training Level')
            ->options(Equipping::getTrainingOptions());
    }

    private static function personTypeFilter(): SelectFilter
    {
        return SelectFilter::make('equippable_type')
            ->label('Person Type')
            ->options([
                'App\\Models\\Leader' => 'Leader',
                'App\\Models\\CellMember' => 'Cell Member',
            ]);
    }

    private static function hasCellGroupFilter()
    {
        return Tables\Filters\TernaryFilter::make('have_cell_group')
            ->label('Has Cell Group');
    }

    /**
     * Get table actions configuration
     */
    public static function actions(): array
    {
        return [
            // Edit and Delete actions are disabled for security/read-only access
        ];
    }

    /**
     * Get table bulk actions configuration
     */
    public static function bulkActions(): array
    {
        return [
            // Bulk actions disabled for security
        ];
    }

    /**
     * Get default sort configuration
     */
    public static function defaultSort(): array
    {
        return ['created_at', 'desc'];
    }
}

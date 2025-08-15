<?php

namespace App\Filament\Resources\ConsolidationResource\Tables\Columns;

use App\Models\Consolidation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class ConsolidationTableColumns
{
    public static function get(): array
    {
        return [
            self::getVipNameColumn(),
            self::getConsolidatorNameColumn(),
            self::getDirectLeaderColumn(),
            self::getConsolidationDateColumn(),
            self::getConsolidationPlaceColumn(),
            self::getVipStatusColumn(),
            self::getVipContactDetailsColumn(),
            self::getSuynlProgressColumn(),
            self::getSundayServicesProgressColumn(),
            self::getCellGroupProgressColumn(),
            self::getCreatedAtColumn(),
        ];
    }

    private static function getVipNameColumn(): TextColumn
    {
        return TextColumn::make('vip_name')
            ->label('VIP Name')
            ->searchable()
            ->sortable();
    }

    private static function getConsolidatorNameColumn(): TextColumn
    {
        return TextColumn::make('consolidator_name')
            ->label('Consolidator Name')
            ->getStateUsing(function (Consolidation $record): string {
                return $record->consolidator?->name . ' (' . 
                       ($record->consolidator_type === 'App\\Models\\Leader' ? 'Leader' : 'Cell Member') . ')' ?? 'N/A';
            });
    }

    private static function getDirectLeaderColumn(): TextColumn
    {
        return TextColumn::make('direct_leader')
            ->label('Direct Leader')
            ->getStateUsing(function (Consolidation $record): string {
                if ($record->consolidator_type === 'App\\Models\\Leader') {
                    return $record->consolidator?->parentLeader?->name ?? 'Top Level';
                } elseif ($record->consolidator_type === 'App\\Models\\CellMember') {
                    return $record->consolidator?->leader?->name ?? 'No Leader';
                }
                return 'N/A';
            });
    }

    private static function getConsolidationDateColumn(): TextColumn
    {
        return TextColumn::make('consolidation_date')
            ->label('Date')
            ->date()
            ->sortable();
    }

    private static function getConsolidationPlaceColumn(): BadgeColumn
    {
        return BadgeColumn::make('consolidation_place')
            ->label('Place')
            ->colors([
                'primary' => 'services',
                'success' => 'cell_group',
                'warning' => 'ove',
            ]);
    }

    private static function getVipStatusColumn(): BadgeColumn
    {
        return BadgeColumn::make('vip_status')
            ->label('VIP Status')
            ->colors([
                'info' => 'other_church',
                'success' => 'new_christian',
                'warning' => 'recommitment',
            ]);
    }

    private static function getVipContactDetailsColumn(): TextColumn
    {
        return TextColumn::make('vip_contact_details')
            ->label('Contact')
            ->limit(30)
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function getSuynlProgressColumn(): TextColumn
    {
        return TextColumn::make('SUYNL Progress')
            ->label('SUYNL')
            ->getStateUsing(function (Consolidation $record): string {
                return $record->getSuynlProgressFormatted();
            })
            ->badge()
            ->color(function (string $state): string {
                $progress = (int) explode('/', $state)[0];
                if ($progress >= 8) return 'success';
                if ($progress >= 5) return 'warning';
                if ($progress > 0) return 'info';
                return 'gray';
            });
    }

    private static function getSundayServicesProgressColumn(): TextColumn
    {
        return TextColumn::make('Services')
            ->getStateUsing(function (Consolidation $record): string {
                return $record->getSundayServiceProgressFormatted();
            })
            ->badge()
            ->color(function (string $state): string {
                $progress = (int) explode('/', $state)[0];
                if ($progress === 4) return 'success';
                if ($progress >= 2) return 'warning';
                if ($progress > 0) return 'info';
                return 'gray';
            });
    }

    private static function getCellGroupProgressColumn(): TextColumn
    {
        return TextColumn::make('Cell Group')
            ->label('Cell Group')
            ->getStateUsing(function (Consolidation $record): string {
                return $record->getCellGroupProgressFormatted();
            })
            ->badge()
            ->color(function (string $state): string {
                $progress = (int) explode('/', $state)[0];
                if ($progress === 4) return 'success';
                if ($progress >= 2) return 'warning';
                if ($progress > 0) return 'info';
                return 'gray';
            });
    }

    private static function getCreatedAtColumn(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label('Created')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }
}

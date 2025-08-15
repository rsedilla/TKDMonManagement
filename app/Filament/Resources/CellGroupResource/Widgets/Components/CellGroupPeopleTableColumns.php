<?php

namespace App\Filament\Resources\CellGroupResource\Widgets\Components;

use Filament\Tables;
use App\Models\CellMember;
use App\Models\Leader;

class CellGroupPeopleTableColumns
{
    public static function get(): array
    {
        return [
            self::personTypeColumn(),
            self::nameColumn(),
            self::ageColumn(),
            self::networkColumn(),
            self::civilStatusColumn(),
            self::directLeaderColumn(),
            self::hierarchyColumn(),
            self::trainingColumn(),
            self::cellGroupColumn(),
            self::statusColumn(),
            self::enrollmentDateColumn(),
            self::notesColumn(),
        ];
    }

    private static function personTypeColumn()
    {
        return Tables\Columns\TextColumn::make('person_type')
            ->label('Role')
            ->badge()
            ->color(fn (?string $state): string => match ($state) {
                'Cell Member' => 'info',
                'Leader' => 'warning',
                default => 'gray',
            })
            ->placeholder('N/A');
    }

    private static function nameColumn()
    {
        return Tables\Columns\TextColumn::make('name')
            ->label('Name')
            ->searchable()
            ->sortable()
            ->weight('medium');
    }

    private static function ageColumn()
    {
        return Tables\Columns\TextColumn::make('age')
            ->label('Age')
            ->numeric()
            ->sortable()
            ->placeholder('N/A');
    }

    private static function networkColumn()
    {
        return Tables\Columns\TextColumn::make('network')
            ->label('Network')
            ->badge()
            ->color(fn (?string $state): string => match ($state) {
                'mens' => 'blue',
                'womens' => 'pink',
                default => 'gray',
            })
            ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'N/A')
            ->placeholder('N/A');
    }

    private static function civilStatusColumn()
    {
        return Tables\Columns\TextColumn::make('civil_status')
            ->label('Civil Status')
            ->badge()
            ->color(fn (?string $state): string => match ($state) {
                'single' => 'info',
                'married' => 'success',
                'widow' => 'warning',
                default => 'gray',
            })
            ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'N/A')
            ->placeholder('N/A');
    }

    private static function directLeaderColumn()
    {
        return Tables\Columns\TextColumn::make('leader.name')
            ->label('Direct Leader')
            ->getStateUsing(function ($record) {
                if ($record->person_type === 'Leader') {
                    // For leaders, find their parent leader
                    $parentLeader = Leader::find($record->leader_id);
                    return $parentLeader ? $parentLeader->name : 'No parent leader';
                } else {
                    // For cell members, find their leader
                    $leader = Leader::find($record->leader_id);
                    return $leader ? $leader->name : 'No leader assigned';
                }
            })
            ->badge()
            ->color('success')
            ->placeholder('No leader assigned');
    }

    private static function hierarchyColumn()
    {
        return Tables\Columns\TextColumn::make('leader_hierarchy')
            ->label('Leadership Chain')
            ->getStateUsing(function ($record) {
                if ($record->person_type === 'Leader') {
                    $leader = Leader::find($record->id);
                    if ($leader) {
                        $breadcrumb = $leader->getHierarchyBreadcrumb();
                        return implode(' → ', $breadcrumb);
                    }
                    return 'No hierarchy';
                } else {
                    $leader = Leader::find($record->leader_id);
                    if ($leader) {
                        $breadcrumb = $leader->getHierarchyBreadcrumb();
                        return implode(' → ', $breadcrumb);
                    }
                    return 'No assignment';
                }
            })
            ->color('secondary')
            ->wrap()
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function trainingColumn()
    {
        return Tables\Columns\TextColumn::make('equipping.training_attended')
            ->label('Training Level')
            ->getStateUsing(function ($record) {
                if ($record->person_type === 'Leader') {
                    $leader = Leader::with('equipping')->find($record->id);
                    return $leader->equipping->training_attended ?? 'Not specified';
                } else {
                    $cellMember = CellMember::with('equipping')->find($record->id);
                    return $cellMember->equipping->training_attended ?? 'Not specified';
                }
            })
            ->badge()
            ->color(fn (?string $state): string => match ($state) {
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

    private static function cellGroupColumn()
    {
        return Tables\Columns\BooleanColumn::make('equipping.have_cell_group')
            ->label('Have Cell Group')
            ->getStateUsing(function ($record) {
                if ($record->person_type === 'Leader') {
                    $leader = Leader::with('equipping')->find($record->id);
                    return $leader->equipping->have_cell_group ?? false;
                } else {
                    $cellMember = CellMember::with('equipping')->find($record->id);
                    return $cellMember->equipping->have_cell_group ?? false;
                }
            })
            ->placeholder('Not specified')
            ->toggleable(isToggledHiddenByDefault: true);
    }

    private static function statusColumn()
    {
        return Tables\Columns\IconColumn::make('status')
            ->label('Active')
            ->boolean()
            ->trueIcon('heroicon-o-check-circle')
            ->falseIcon('heroicon-o-x-circle')
            ->trueColor('success')
            ->falseColor('danger');
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
            ->limit(30)
            ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                $state = $column->getState();
                if (strlen($state) <= 30) {
                    return null;
                }
                return $state;
            })
            ->toggleable(isToggledHiddenByDefault: true);
    }
}

<?php

namespace App\Filament\Resources\CellMemberResource\Tables\Components;

use App\Models\Leader;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class CellMemberTableFilters
{
    public static function get(): array
    {
        return [
            self::statusFilter(),
            self::leaderFilter(),
            self::networkFilter(),
            self::trainingFilter(),
            self::cellGroupFilter(),
            self::networkLeaderFilter(),
            self::unassignedFilter(),
        ];
    }

    private static function statusFilter()
    {
        return Tables\Filters\TernaryFilter::make('status')
            ->label('Active Status')
            ->boolean()
            ->trueLabel('Active cell members')
            ->falseLabel('Inactive cell members')
            ->placeholder('All cell members');
    }

    private static function leaderFilter()
    {
        return Tables\Filters\SelectFilter::make('leader')
            ->relationship('leader', 'name')
            ->searchable()
            ->preload()
            ->label('Direct Cell Leader');
    }

    private static function networkFilter()
    {
        return Tables\Filters\SelectFilter::make('network')
            ->options([
                'mens' => 'Mens',
                'womens' => 'Womens',
            ]);
    }

    private static function trainingFilter()
    {
        return Tables\Filters\SelectFilter::make('training_attended')
            ->options([
                'SUYNL' => 'SUYNL',
                'LIFECLASS' => 'LIFECLASS',
                'ENCOUNTER' => 'ENCOUNTER',
                'SOL1' => 'SOL1',
                'SOL2' => 'SOL2',
                'SOL3' => 'SOL3',
                'SOL GRADUATE' => 'SOL GRADUATE',
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query->when(
                    $data['value'],
                    fn (Builder $query, $value): Builder => $query->whereHas('equipping', function (Builder $query) use ($value) {
                        $query->where('training_attended', $value);
                    })
                );
            })
            ->label('Training Attended')
            ->indicator('Training');
    }

    private static function cellGroupFilter()
    {
        return Tables\Filters\TernaryFilter::make('have_cell_group')
            ->label('Have Cell Group')
            ->boolean()
            ->trueLabel('Has cell group')
            ->falseLabel('No cell group')
            ->placeholder('All members')
            ->query(function (Builder $query, array $data): Builder {
                return $query->when(
                    isset($data['value']),
                    fn (Builder $query): Builder => $query->whereHas('equipping', function (Builder $query) use ($data) {
                        $query->where('have_cell_group', $data['value']);
                    })
                );
            })
            ->indicator('Cell Group Status');
    }

    private static function networkLeaderFilter()
    {
        return Tables\Filters\SelectFilter::make('network_leader')
            ->label('Network Cell Leader (Including Hierarchy)')
            ->options(fn () => Leader::pluck('name', 'id'))
            ->searchable()
            ->query(function (Builder $query, array $data): Builder {
                if (!$data['value']) {
                    return $query;
                }
                
                $leader = Leader::find($data['value']);
                if (!$leader) {
                    return $query;
                }
                
                return $query->where(function($q) use ($leader) {
                    // Direct cell members under this leader
                    $q->where('leader_id', $leader->id)
                      // OR cell members under any descendant leader in the network
                      ->orWhereHas('leader', function ($subQuery) use ($leader) {
                          $subQuery->where('path', 'LIKE', $leader->path . $leader->id . '/%');
                      });
                });
            })
            ->indicator('Network Leader');
    }

    private static function unassignedFilter()
    {
        return Tables\Filters\Filter::make('unassigned')
            ->label('Unassigned Cell Members')
            ->query(fn (Builder $query): Builder => $query->whereNull('leader_id'))
            ->toggle();
    }
}

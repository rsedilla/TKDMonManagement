<?php

namespace App\Filament\Resources\CellGroupResource\Widgets\Components;

use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class CellGroupPeopleTableFilters
{
    public static function get(): array
    {
        return [
            self::statusFilter(),
            self::networkFilter(),
            self::trainingFilter(),
        ];
    }

    private static function statusFilter()
    {
        return Tables\Filters\TernaryFilter::make('status')
            ->label('Active Status')
            ->boolean()
            ->trueLabel('Active')
            ->falseLabel('Inactive')
            ->placeholder('All');
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
            ->label('Training Level');
    }
}

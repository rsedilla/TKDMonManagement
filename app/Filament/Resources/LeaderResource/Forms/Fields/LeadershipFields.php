<?php

namespace App\Filament\Resources\LeaderResource\Forms\Fields;

use App\Filament\Shared\Fields\CommonStatusFields;
use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms;

class LeadershipFields
{
    public static function networkGrid(): Grid
    {
        return Grid::make(1)
            ->schema([
                CommonStatusFields::networkField(),
            ]);
    }

    public static function civilStatusField(): Select
    {
        return CommonStatusFields::civilStatusField();
    }

    public static function parentLeaderField(): Select
    {
        return Select::make('parent_leader_id')
            ->label('Reports To')
            ->relationship('parentLeader', 'name')
            ->searchable()
            ->preload()
            ->placeholder('Select parent leader (leave empty for top-level)')
            ->helperText('Choose the leader this person reports to')
            ->createOptionForm([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ])
            ->disabled(FormHelpers::getDisabledClosure());
    }

    public static function cellGroupField(): Select
    {
        return Select::make('cell_group_id')
            ->label('Cell Group')
            ->relationship(
                name: 'cellGroup', 
                titleAttribute: 'cell_group_id',
                modifyQueryUsing: fn ($query) => $query->with('leader')
            )
            ->getOptionLabelFromRecordUsing(fn ($record) => 
                $record->cell_group_id . ' - ' . ($record->leader->name ?? 'No Leader')
            )
            ->getSearchResultsUsing(function (string $search) {
                return \App\Models\CellGroup::with('leader')
                    ->where(function($query) use ($search) {
                        $query->where('cell_group_id', 'like', "%{$search}%")
                              ->orWhereHas('leader', function($q) use ($search) {
                                  $q->where('name', 'like', "%{$search}%");
                              });
                    })
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn ($record) => [
                        $record->id => $record->cell_group_id . ' - ' . ($record->leader->name ?? 'No Leader')
                    ]);
            })
            ->searchable()
            ->preload()
            ->placeholder('Select a cell group (optional)')
            ->helperText('Search by cell group ID or leader name')
            ->disabled(FormHelpers::getDisabledClosure());
    }
}

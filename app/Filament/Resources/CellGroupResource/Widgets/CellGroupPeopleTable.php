<?php

namespace App\Filament\Resources\CellGroupResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\CellMember;
use App\Models\Leader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CellGroupPeopleTable extends BaseWidget
{
    public $record;
    protected $allPeopleData;
    protected $subordinateLeaders;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        // Get cell members for this cell group
        $cellMembers = CellMember::where('cell_group_id', $this->record->id)
            ->select([
                DB::raw("'Cell Member' as person_type"),
                'id',
                'name',
                'age',
                'birthday',
                'network',
                'civil_status',
                'leader_id',
                'cell_group_id',
                'status',
                'enrollment_date',
                'notes',
                'created_at',
                'updated_at'
            ]);

        // Get leaders for this cell group  
        $leaders = Leader::where('cell_group_id', $this->record->id)
            ->select([
                DB::raw("'Leader' as person_type"),
                'id',
                'name',
                'age',
                'birthday',
                'network',
                'civil_status',
                'parent_leader_id as leader_id',
                'cell_group_id',
                'status',
                'created_at as enrollment_date',
                DB::raw('NULL as notes'),
                'created_at',
                'updated_at'
            ]);

        // Create the union query
        $unionQuery = $cellMembers->unionAll($leaders);
        
        // Use withoutGlobalScopes to bypass soft delete constraints on the outer query
        return CellMember::withoutGlobalScopes()
            ->fromSub($unionQuery, 'people')
            ->orderBy('person_type', 'DESC')
            ->orderBy('name');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('person_type')
                ->label('Role')
                ->badge()
                ->color(fn (?string $state): string => match ($state) {
                    'Cell Member' => 'info',
                    'Leader' => 'warning',
                    default => 'gray',
                })
                ->placeholder('N/A'),

            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable()
                ->weight('medium'),

            Tables\Columns\TextColumn::make('age')
                ->label('Age')
                ->numeric()
                ->sortable()
                ->placeholder('N/A'),

            Tables\Columns\TextColumn::make('network')
                ->label('Network')
                ->badge()
                ->color(fn (?string $state): string => match ($state) {
                    'mens' => 'blue',
                    'womens' => 'pink',
                    default => 'gray',
                })
                ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'N/A')
                ->placeholder('N/A'),

            Tables\Columns\TextColumn::make('civil_status')
                ->label('Civil Status')
                ->badge()
                ->color(fn (?string $state): string => match ($state) {
                    'single' => 'info',
                    'married' => 'success',
                    'widow' => 'warning',
                    default => 'gray',
                })
                ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'N/A')
                ->placeholder('N/A'),

            Tables\Columns\TextColumn::make('leader.name')
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
                ->placeholder('No leader assigned'),

            Tables\Columns\TextColumn::make('leader_hierarchy')
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
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('equipping.training_attended')
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
                ->placeholder('Not specified'),

            Tables\Columns\BooleanColumn::make('equipping.have_cell_group')
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
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\IconColumn::make('status')
                ->label('Active')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),

            Tables\Columns\TextColumn::make('enrollment_date')
                ->label('Join Date')
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('notes')
                ->label('Notes')
                ->limit(30)
                ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                    $state = $column->getState();
                    if (strlen($state) <= 30) {
                        return null;
                    }
                    return $state;
                })
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\TernaryFilter::make('status')
                ->label('Active Status')
                ->boolean()
                ->trueLabel('Active')
                ->falseLabel('Inactive')
                ->placeholder('All'),

            Tables\Filters\SelectFilter::make('network')
                ->options([
                    'mens' => 'Mens',
                    'womens' => 'Womens',
                ]),

            Tables\Filters\SelectFilter::make('training_attended')
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
                ->label('Training Level'),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Cell Group ' . $this->record->cell_group_id;
    }

    protected function getTableDescription(): string
    {
        // Count people directly assigned to this cell group
        $cellMemberCount = CellMember::where('cell_group_id', $this->record->id)->count();
        $activeCellMembers = CellMember::where('cell_group_id', $this->record->id)->where('status', true)->count();
        
        $leaderCount = Leader::where('cell_group_id', $this->record->id)->count();
        $activeLeaders = Leader::where('cell_group_id', $this->record->id)->where('status', true)->count();
        
        // Total people in this specific cell group
        $totalPeople = $cellMemberCount + $leaderCount;
        $totalActive = $activeCellMembers + $activeLeaders;
        
        $cellGroupLeader = $this->record->leader;
        $leaderName = $cellGroupLeader ? $cellGroupLeader->name : 'No Leader Assigned';
        
        return "Total People: {$totalPeople} | Active: {$totalActive} | Cell Group Leader: {$leaderName}";
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

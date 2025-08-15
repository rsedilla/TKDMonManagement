<?php

namespace App\Filament\Resources\CellGroupResource\Widgets\Components;

use App\Models\CellMember;
use App\Models\Leader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CellGroupPeopleTableQuery
{
    public static function get($record): Builder
    {
        // Get cell members for this cell group
        $cellMembers = CellMember::where('cell_group_id', $record->id)
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
        $leaders = Leader::where('cell_group_id', $record->id)
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
}

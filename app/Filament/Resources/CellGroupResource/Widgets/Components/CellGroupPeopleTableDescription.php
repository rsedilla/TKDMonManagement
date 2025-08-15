<?php

namespace App\Filament\Resources\CellGroupResource\Widgets\Components;

use App\Models\CellMember;
use App\Models\Leader;

class CellGroupPeopleTableDescription
{
    public static function get($record): string
    {
        // Count people directly assigned to this cell group
        $cellMemberCount = CellMember::where('cell_group_id', $record->id)->count();
        $activeCellMembers = CellMember::where('cell_group_id', $record->id)->where('status', true)->count();
        
        $leaderCount = Leader::where('cell_group_id', $record->id)->count();
        $activeLeaders = Leader::where('cell_group_id', $record->id)->where('status', true)->count();
        
        // Total people in this specific cell group
        $totalPeople = $cellMemberCount + $leaderCount;
        $totalActive = $activeCellMembers + $activeLeaders;
        
        $cellGroupLeader = $record->leader;
        $leaderName = $cellGroupLeader ? $cellGroupLeader->name : 'No Leader Assigned';
        
        return "Total People: {$totalPeople} | Active: {$totalActive} | Cell Group Leader: {$leaderName}";
    }

    public static function getHeading($record): string
    {
        return 'Cell Group ' . $record->cell_group_id;
    }
}

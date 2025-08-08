<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CellGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'cell_group_id',
        'leader_id',
        'cell_group_type',
        'meeting_day',
        'meeting_time',
        'meeting_location',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meeting_time' => 'datetime:H:i',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cellGroup) {
            if (empty($cellGroup->cell_group_id)) {
                $cellGroup->cell_group_id = self::generateCellGroupId();
            }
        });
    }

    /**
     * Generate a unique cell group ID
     */
    private static function generateCellGroupId()
    {
        do {
            // Generate ID with format: YYYYMM#### (Year + Month + 4-digit sequence)
            $year = date('Y');
            $month = date('m');
            
            // Get count of cell groups created in current year and month
            $currentYearMonth = $year . $month;
            $monthlyCount = self::where('cell_group_id', 'LIKE', $currentYearMonth . '%')->count();
            
            $sequence = str_pad($monthlyCount + 1, 4, '0', STR_PAD_LEFT);
            $cellGroupId = $currentYearMonth . $sequence;
        } while (self::where('cell_group_id', $cellGroupId)->exists());

        return $cellGroupId;
    }

    // Relationship to the cell leader
    public function leader()
    {
        return $this->belongsTo(Leader::class);
    }

    // Get all cell members in this cell group through the leader
    public function cellMembers()
    {
        return $this->hasMany(CellMember::class, 'cell_group_id');
    }

    // Get all leaders assigned to this cell group
    public function leaders()
    {
        return $this->hasMany(Leader::class, 'cell_group_id');
    }

    // Get total count of people in this cell group (members + leaders)
    public function getTotalMembersCount()
    {
        return $this->cellMembers()->count() + $this->leaders()->count();
    }

    // Scope for active cell groups
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific cell group type
    public function scopeOfType($query, $type)
    {
        return $query->where('cell_group_type', $type);
    }
}

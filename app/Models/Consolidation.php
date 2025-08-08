<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consolidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vip_name',
        'vip_contact_details',
        'vip_address',
        'consolidator_id',
        'consolidator_type',
        'consolidation_date',
        'consolidation_place',
        'vip_status',
        'suynl_lessons_completed',
        'sunday_services_attended',
        'cell_group_attended',
        // SUYNL lesson date fields
        'suynl_lesson_1_date',
        'suynl_lesson_2_date',
        'suynl_lesson_3_date',
        'suynl_lesson_4_date',
        'suynl_lesson_5_date',
        'suynl_lesson_6_date',
        'suynl_lesson_7_date',
        'suynl_lesson_8_date',
        'suynl_lesson_9_date',
        'suynl_lesson_10_date',
        // Sunday service date fields
        'sunday_service_1_date',
        'sunday_service_2_date',
        'sunday_service_3_date',
        'sunday_service_4_date',
        // Cell group date fields
        'cell_group_1_date',
        'cell_group_2_date',
        'cell_group_3_date',
        'cell_group_4_date',
    ];

    protected $casts = [
        'consolidation_date' => 'date',
        'suynl_lessons_completed' => 'array',
        'sunday_services_attended' => 'array',
        'cell_group_attended' => 'array',
        // Cast all new date fields
        'suynl_lesson_1_date' => 'date',
        'suynl_lesson_2_date' => 'date',
        'suynl_lesson_3_date' => 'date',
        'suynl_lesson_4_date' => 'date',
        'suynl_lesson_5_date' => 'date',
        'suynl_lesson_6_date' => 'date',
        'suynl_lesson_7_date' => 'date',
        'suynl_lesson_8_date' => 'date',
        'suynl_lesson_9_date' => 'date',
        'suynl_lesson_10_date' => 'date',
        'sunday_service_1_date' => 'date',
        'sunday_service_2_date' => 'date',
        'sunday_service_3_date' => 'date',
        'sunday_service_4_date' => 'date',
        'cell_group_1_date' => 'date',
        'cell_group_2_date' => 'date',
        'cell_group_3_date' => 'date',
        'cell_group_4_date' => 'date',
    ];

    // Constants for dropdown options
    public const CONSOLIDATION_PLACES = [
        'services' => 'Services',
        'cell_group' => 'Cell Group',
        'ove' => 'OVE',
    ];

    public const VIP_STATUSES = [
        'other_church' => 'Other Church',
        'new_christian' => 'New Christian',
        'recommitment' => 'Recommitment',
    ];

    // Polymorphic relationship to consolidator (Leader or CellMember)
    public function consolidator()
    {
        return $this->morphTo();
    }

    // Helper method to get consolidator's direct leader name
    public function getConsolidatorDirectLeaderNameAttribute()
    {
        if ($this->consolidator_type === 'App\\Models\\CellMember') {
            return $this->consolidator?->leader?->name ?? 'No Leader';
        } elseif ($this->consolidator_type === 'App\\Models\\Leader') {
            return $this->consolidator?->parentLeader?->name ?? 'Top Level';
        }
        return 'N/A';
    }

    // Scope to get consolidators with advanced SOL training (SOL2, SOL3, or SOL GRADUATE)
    public static function getConsolidatorOptions()
    {
        $qualifiedTrainingLevels = ['SOL2', 'SOL3', 'SOL GRADUATE'];
        
        $leaders = Leader::whereHas('equippings', function ($query) use ($qualifiedTrainingLevels) {
            $query->whereIn('training_attended', $qualifiedTrainingLevels);
        })->get(['id', 'name']);

        $cellMembers = CellMember::whereHas('equippings', function ($query) use ($qualifiedTrainingLevels) {
            $query->whereIn('training_attended', $qualifiedTrainingLevels);
        })->get(['id', 'name']);

        $options = [];
        
        foreach ($leaders as $leader) {
            $options['App\\Models\\Leader:' . $leader->id] = $leader->name . ' (Leader)';
        }

        foreach ($cellMembers as $cellMember) {
            $options['App\\Models\\CellMember:' . $cellMember->id] = $cellMember->name . ' (Cell Member)';
        }

        return $options;
    }

    // Helper methods for dropdown options
    public static function getConsolidationPlaceOptions()
    {
        return self::CONSOLIDATION_PLACES;
    }

    public static function getVipStatusOptions()
    {
        return self::VIP_STATUSES;
    }

    // Helper methods for the new tracking features
    public static function getSuynlLessonOptions()
    {
        return collect(range(1, 10))->mapWithKeys(function ($lesson) {
            return [$lesson => "L{$lesson}"];
        })->toArray();
    }

    public static function getSundayServiceOptions()
    {
        return collect(range(1, 4))->mapWithKeys(function ($service) {
            return [$service => "S{$service}"];
        })->toArray();
    }

    public static function getCellGroupAttendanceOptions()
    {
        return collect(range(1, 4))->mapWithKeys(function ($session) {
            return [$session => "CG{$session}"];
        })->toArray();
    }

    // Progress tracking methods - updated to use date fields
    public function getSuynlProgressPercentage()
    {
        $completedCount = $this->getSuynlCompletedCount();
        return ($completedCount / 10) * 100;
    }

    public function getSundayServiceProgressPercentage()
    {
        $completedCount = $this->getSundayServiceCompletedCount();
        return ($completedCount / 4) * 100;
    }

    public function getCellGroupProgressPercentage()
    {
        $completedCount = $this->getCellGroupCompletedCount();
        return ($completedCount / 4) * 100;
    }

    // Helper methods to count completed items based on date fields
    public function getSuynlCompletedCount()
    {
        $count = 0;
        for ($i = 1; $i <= 10; $i++) {
            if ($this->{"suynl_lesson_{$i}_date"}) {
                $count++;
            }
        }
        return $count;
    }

    public function getSundayServiceCompletedCount()
    {
        $count = 0;
        for ($i = 1; $i <= 4; $i++) {
            if ($this->{"sunday_service_{$i}_date"}) {
                $count++;
            }
        }
        return $count;
    }

    public function getCellGroupCompletedCount()
    {
        $count = 0;
        for ($i = 1; $i <= 4; $i++) {
            if ($this->{"cell_group_{$i}_date"}) {
                $count++;
            }
        }
        return $count;
    }

    // Formatted progress display methods
    public function getSuynlProgressFormatted()
    {
        return $this->getSuynlCompletedCount() . '/10';
    }

    public function getSundayServiceProgressFormatted()
    {
        return $this->getSundayServiceCompletedCount() . '/4';
    }

    public function getCellGroupProgressFormatted()
    {
        return $this->getCellGroupCompletedCount() . '/4';
    }

    // Scope for filtering by consolidation place
    public function scopeByPlace($query, $place)
    {
        return $query->where('consolidation_place', $place);
    }

    // Scope for filtering by VIP status
    public function scopeByVipStatus($query, $status)
    {
        return $query->where('vip_status', $status);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CellMember extends Model
{
    use HasFactory, SoftDeletes;
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($cellMember) {
            if ($cellMember->equipping()->exists()) {
                throw new \Exception('Cannot delete: This cell member is referenced in Equipping.');
            }
        });
        // Automatically calculate age from birthday when saving
        static::saving(function ($cellMember) {
            if ($cellMember->birthday && !$cellMember->age) {
                $cellMember->age = Carbon::parse($cellMember->birthday)->age;
            }
        });
    }

    protected $fillable = [
        'name',
        'age',
        'birthday',
        'network',
        'civil_status',
        'status',
        'leader_id',
        'cell_group_id',
        'notes',
        'enrollment_date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'enrollment_date' => 'date',
        'birthday' => 'date',
    ];

    // (Merged into the first boot() method at the top of the file)

    // Automatically calculate age from birthday
    public function getAgeAttribute($value)
    {
        if ($this->birthday && !$value) {
            return $this->birthday->age;
        }
        return $value; // return stored age if available
    }

    public function leader()
    {
        return $this->belongsTo(Leader::class);
    }

    public function cellGroup()
    {
        return $this->belongsTo(CellGroup::class);
    }

    public function equipping()
    {
        return $this->morphOne(Equipping::class, 'equippable');
    }

    public function equippings()
    {
        return $this->morphMany(Equipping::class, 'equippable');
    }
}

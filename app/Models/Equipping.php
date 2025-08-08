<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'equippable_type',
        'equippable_id',
        'training_attended',
        'have_cell_group',
    ];

    protected $casts = [
        'training_attended' => 'string',
        'have_cell_group' => 'boolean',
    ];

    // Define the training levels as constants
    public const TRAINING_LEVELS = [
        'SUYNL' => 'SUYNL',
        'LIFECLASS' => 'LIFECLASS',
        'ENCOUNTER' => 'ENCOUNTER',
        'SOL1' => 'SOL1',
        'SOL2' => 'SOL2',
        'SOL3' => 'SOL3',
        'SOL GRADUATE' => 'SOL GRADUATE',
    ];

    public const CELL_GROUP_STATUS = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
    ];

    // Polymorphic relationship
    public function equippable()
    {
        // Standard: include withTrashed for soft-deletable related models
        return $this->morphTo()->morphWith([
            \App\Models\Leader::class => ['withTrashed'],
            \App\Models\CellMember::class => ['withTrashed'],
        ]);
    }

    // Helper methods
    public static function getTrainingOptions()
    {
        return self::TRAINING_LEVELS;
    }

    public static function getCellGroupOptions()
    {
        return self::CELL_GROUP_STATUS;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Leader extends Model
{
    use HasFactory, SoftDeletes;
    protected static function boot()
    {
        parent::boot();

        // Automatically calculate age from birthday when saving
        static::saving(function ($leader) {
            if ($leader->birthday && !$leader->age) {
                $leader->age = Carbon::parse($leader->birthday)->age;
            }
        });

        // Existing hierarchy logic
        static::creating(function ($leader) {
            if (!isset($leader->level)) {
                if ($leader->parent_leader_id) {
                    $parent = self::find($leader->parent_leader_id);
                    if ($parent) {
                        $leader->level = $parent->level + 1;
                        $leader->path = $parent->path . $parent->id . '/';
                    }
                } else {
                    $leader->level = 0;
                    $leader->path = '/';
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'network',
        'email',
        'phone',
        'age',
        'birthday',
        'bio',
        'civil_status',
        'status',
        'parent_leader_id', // Add this for the hierarchy
        'level', // Track hierarchy level for performance
        'path', // Store full path for quick queries
        'cell_group_id', // Add cell group assignment
    ];

    protected $casts = [
        'status' => 'boolean',
        'level' => 'integer',
        'birthday' => 'date',
    ];

    // Automatically calculate age from birthday
    public function getAgeAttribute($value)
    {
        if ($this->birthday && !$value) {
            return $this->birthday->age;
        }
        return $value; // return stored age if available
    }

    public function cellMembers()
    {
        return $this->hasMany(CellMember::class);
    }

    // Relationship to cell groups led by this leader
    public function cellGroups()
    {
        return $this->hasMany(CellGroup::class);
    }

    // Relationship to the cell group this leader belongs to
    public function cellGroup()
    {
        return $this->belongsTo(CellGroup::class);
    }

    // Polymorphic relationship to equipping records
    public function equippings()
    {
        return $this->morphMany(Equipping::class, 'equippable');
    }

    // Hierarchical relationships for leaders
    public function parentLeader()
    {
        return $this->belongsTo(Leader::class, 'parent_leader_id');
    }

    public function childLeaders()
    {
        return $this->hasMany(Leader::class, 'parent_leader_id');
    }

    // Get all descendants efficiently using path-based queries
    public function allChildLeaders()
    {
        return self::where('path', 'LIKE', $this->path . $this->id . '/%');
    }

    // Get all ancestors efficiently
    public function allParentLeaders()
    {
        if (!$this->path) return collect();
        
        $parentIds = array_filter(explode('/', trim($this->path, '/')));
        return self::whereIn('id', $parentIds)->orderBy('level');
    }

    // Get total network size (scalable for large hierarchies)
    public function getNetworkSize()
    {
        $cellMembersCount = $this->cellMembers()->count();
        $descendantLeadersCount = $this->allChildLeaders()->count();
        $descendantCellMembersCount = CellMember::whereHas('leader', function ($query) {
            $query->where('path', 'LIKE', $this->path . $this->id . '/%');
        })->count();

        return $cellMembersCount + $descendantLeadersCount + $descendantCellMembersCount;
    }

    // Helper method to get the hierarchy breadcrumb
    public function getHierarchyBreadcrumb()
    {
        $ancestors = $this->allParentLeaders();
        $breadcrumb = $ancestors->pluck('name')->toArray();
        $breadcrumb[] = $this->name;
        
        return $breadcrumb;
    }

    // Update hierarchy information when parent changes
    public function updateHierarchy()
    {
        if ($this->parent_leader_id) {
            $parent = $this->parentLeader;
            $this->level = $parent->level + 1;
            $this->path = $parent->path . $parent->id . '/';
        } else {
            $this->level = 0;
            $this->path = '/';
        }

        $this->save();

        // Update all descendants
        foreach ($this->childLeaders as $child) {
            $child->updateHierarchy();
        }
    }

    // (Merged into the first boot() method at the top of the file)

    // Get performance metrics for this leader's network
    public function getNetworkMetrics()
    {
        return [
            'direct_cell_members' => $this->cellMembers()->count(),
            'direct_leaders' => $this->childLeaders()->count(),
            'total_network_size' => $this->getNetworkSize(),
            'hierarchy_depth' => $this->allChildLeaders()->max('level') - $this->level,
            'active_cell_members' => $this->cellMembers()->where('status', true)->count(),
            'active_leaders' => $this->allChildLeaders()->where('status', true)->count(),
        ];
    }

    /**
     * Check if leader has dependencies that prevent deletion
     */
    public function hasDependencies(): bool
    {
        return $this->equippings()->count() > 0 || 
               $this->cellMembers()->count() > 0 || 
               $this->childLeaders()->count() > 0 || 
               $this->cellGroups()->count() > 0;
    }

    /**
     * Get detailed dependency information
     */
    public function getDependencyInfo(): array
    {
        return [
            'equipping_count' => $this->equippings()->count(),
            'cell_members_count' => $this->cellMembers()->count(),
            'child_leaders_count' => $this->childLeaders()->count(),
            'cell_groups_count' => $this->cellGroups()->count(),
        ];
    }

    /**
     * Get dependency summary as text
     */
    public function getDependencySummary(): string
    {
        $info = $this->getDependencyInfo();
        $dependencies = [];
        
        if ($info['equipping_count'] > 0) {
            $dependencies[] = "{$info['equipping_count']} equipping record(s)";
        }
        if ($info['cell_members_count'] > 0) {
            $dependencies[] = "{$info['cell_members_count']} cell member(s)";
        }
        if ($info['child_leaders_count'] > 0) {
            $dependencies[] = "{$info['child_leaders_count']} subordinate leader(s)";
        }
        if ($info['cell_groups_count'] > 0) {
            $dependencies[] = "{$info['cell_groups_count']} cell group(s)";
        }
        
        return implode(', ', $dependencies);
    }

    public function equipping()
    {
        return $this->morphOne(Equipping::class, 'equippable');
    }
}

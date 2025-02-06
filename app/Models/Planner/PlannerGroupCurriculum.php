<?php

namespace App\Models\Planner;

use App\Models\Grouping\Group;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlannerGroupCurriculum extends Pivot
{
    use HasFactory;

    protected $table = 'planner_group_curricula';

    protected $fillable = [
        'group_id',
        'planner_curriculum_id',
        'is_active',
        'sort',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(PlannerCurriculum::class);
    }
}

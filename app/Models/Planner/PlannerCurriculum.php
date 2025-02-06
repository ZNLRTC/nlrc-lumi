<?php

namespace App\Models\Planner;

use App\Models\Grouping\Group;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planner\PlannerCurriculumContent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PlannerCurriculum extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function curriculumContents(): HasMany
    {
        return $this->hasMany(PlannerCurriculumContent::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'planner_group_curricula')
                    ->using(PlannerGroupCurriculum::class)
                    ->withPivot(['is_active', 'sort']);
    }
}

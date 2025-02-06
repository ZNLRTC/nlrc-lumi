<?php

namespace App\Models\Grouping;

use App\Models\Agencies\Agency;
use App\Models\Courses\Course;
use App\Models\Grouping\GroupType;
use App\Models\Planner\PlannerCurriculum;
use App\Models\Planner\PlannerGroupCurriculum;
use App\Models\Planner\PlannerWeeklySchedule;
use App\Models\Trainee;
use App\Models\Traits\IsActiveTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;
    use IsActiveTrait;

    protected $fillable = [
        'group_type_id',
        'agency_id',
        'active',
        'name',
        'notes',
        'date_of_start',
    ];

    public function getGroupCodeAttribute() {
        if ($this->id !== 1) { // Not beginner's course
            return $this->group_type->code. $this->name;
        } else {
            return $this->name;
        }
    }

    // Collection of active trainees
    public function getActiveTraineesAttribute()
    {
        return $this->trainees()
            ->wherePivot('active', true)
            ->where('trainees.active', true)
            ->get();
    }

    // Needed to show the number of trainees in each group in the list of groups
    public function getActiveTraineeCountAttribute()
    {
        return $this->trainees()
            ->wherePivot('active', true)
            ->wherePivot('trainees.active', true)->count();
    }

    // TODO: UNUSED AT THE MOMENT. CAN PROBABLY BE JUST REMOVED IN LATER COMMITS
    public function getInactiveTraineeCountAttribute()
    {
        return $this->trainees()
            ->wherePivot('active', true)
            ->wherePivot('trainees.active', false)->count();
    }

    // These attribute functions are used in the groups admin panel
    public function getDeployedFlaggedTraineesCountAttribute()
    {
        return $this->trainees()
            ->hasFlag('Deployed')
            ->wherePivot('active', true)
            ->count();
    }

    public function getOnHoldFlaggedTraineesCountAttribute()
    {
        return $this->trainees()
            ->hasFlag('On hold')
            ->wherePivot('active', true)
            ->count();
    }

    public function getQuitFlaggedTraineesCountAttribute()
    {
        return $this->trainees()
            ->hasFlag('Quit')
            ->wherePivot('active', true)
            ->count();
    }

    public function getActiveFlaggedTraineesCountAttribute()
    {
        return $this->trainees()
            ->hasFlag('Active')
            ->wherePivot('active', true)
            ->count();
    }

    public function getInactiveFlaggedTraineesCountAttribute()
    {
        return $this->trainees()
            ->hasFlag('Inactive')
            ->wherePivot('active', true)
            ->count();
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function trainees(): BelongsToMany
    {
        return $this->belongsToMany(Trainee::class)
            ->withTimestamps()
            ->withPivot('added_by');
    }

    public function group_type(): BelongsTo
    {
        return $this->belongsTo(GroupType::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function weeklySchedules(): HasMany
    {
        return $this->hasMany(PlannerWeeklySchedule::class);
    }

    public function curricula(): BelongsToMany
    {
        return $this->belongsToMany(PlannerCurriculum::class, 'planner_group_curricula')
                    ->using(PlannerGroupCurriculum::class)
                    ->withPivot(['is_active', 'sort']);
    }

}

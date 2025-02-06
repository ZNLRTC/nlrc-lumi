<?php

namespace App\Models\Courses;

use App\Models\Courses\Topic;
use App\Models\Courses\Course;
use App\Models\Meetings\Meeting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planner\PlannerWeeklySchedule;
use App\Models\Meetings\Assignments\Assignment;
use App\Models\Planner\PlannerCurriculumContent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'slug',
        'internal_name',
        'description',
        'internal_description',
        'sort',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($unit) {
            $unit->course->clearCache();
        });

        static::deleted(function ($unit) {
            $unit->course->clearCache();
        });

        static::updated(function ($unit) {
            $unit->clearCache();
        });
    }

    // Topic and assignment models call this upon update
    public function clearCache()
    {
        Cache::forget("unit.{$this->id}");
        Cache::forget("unit.{$this->id}.topics");
        Cache::forget("unit.{$this->id}.assignments");
        Cache::forget("course.{$this->course_id}.units");
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function plannerCurriculumContents()
    {
        return $this->belongsToMany(PlannerCurriculumContent::class, 'planner_curriculum_content_unit');
    }

    public function weeklySchedules()
    {
        return PlannerWeeklySchedule::whereJsonContains('units', $this->id)->get();
    }

    public function weeklySchedulesForGroupAndWeek($groupId, $plannerWeekId)
    {
        return PlannerWeeklySchedule::where('group_id', $groupId)
            ->where('planner_week_id', $plannerWeekId)
            ->whereJsonContains('units', $this->id)
            ->get();
    }

}

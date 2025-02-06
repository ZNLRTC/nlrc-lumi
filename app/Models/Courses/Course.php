<?php

namespace App\Models\Courses;

use App\Models\Courses\Unit;
use App\Models\Grouping\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planner\PlannerWeeklySchedule;
use App\Models\Planner\PlannerCurriculumContent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'internal_name',
        'slug',
        'description',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function curriculumContents(): HasMany
    {
        return $this->hasMany(PlannerCurriculumContent::class);
    }

    public function weeklySchedules(): HasMany
    {
        return $this->hasMany(PlannerWeeklySchedule::class);
    }

    // Custom model functions
    protected function get_courses_of_trainee_if_group_is_active($active_group, array $select_fields = []): ?Collection
    {
        $courses = collect([]);

        if ($active_group) {
            $courses = $active_group->group->courses;

            if ($select_fields) {
                $courses = $courses->map(function ($course) use($select_fields) {
                    return $course->only($select_fields);
                });
            }
        }

        return $courses;
    }

    // Course model calls this upon update
    public function clearCache()
    {
        Cache::forget("course.{$this->id}.units");
    }
}

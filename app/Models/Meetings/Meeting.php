<?php

namespace App\Models\Meetings;

use App\Models\Trainee;
use App\Models\Courses\Unit;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planner\PlannerWeeklySchedule;
use App\Models\Planner\PlannerCurriculumContent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'meeting_type_id',
        'description',
        'internal_notes',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function trainees(): BelongsToMany
    {
        return $this->belongsToMany(Trainee::class)
            ->withPivot('meeting_status_id', 'instructor_id', 'internal_notes', 'feedback', 'date')
            ->using(MeetingTrainee::class)
            ->withTimestamps();
    }

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    public function meetingTrainees(): HasMany
    {
        return $this->hasMany(MeetingTrainee::class);
    }

    public function curriculumContents(): BelongsToMany
    {
        return $this->belongsToMany(PlannerCurriculumContent::class, 'planner_curriculum_content_meeting');
    }

    // Method to get weekly schedules containing this meeting
    public function weeklySchedules()
    {
        return PlannerWeeklySchedule::whereJsonContains('meetings', $this->id)->get();
    }

    // Method to get weekly schedules containing this meeting for a specific group and week
    public function weeklySchedulesForGroupAndWeek($groupId, $plannerWeekId)
    {
        return PlannerWeeklySchedule::where('group_id', $groupId)
            ->where('planner_week_id', $plannerWeekId)
            ->whereJsonContains('meetings', $this->id)
            ->get();
    }
}

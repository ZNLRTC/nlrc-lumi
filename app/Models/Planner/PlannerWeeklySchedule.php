<?php

namespace App\Models\Planner;

use App\Enums\Planner\ContentType;
use App\Models\Grouping\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Mail\Mailables\Content;

class PlannerWeeklySchedule extends Model
{
    use HasFactory;

    protected $casts = [
        'units' => 'array',
        'meetings' => 'array',
        'show_custom_content' => 'boolean',
        'content_type' => ContentType::class,
    ];

    protected $fillable = [
        'group_id',
        'planner_week_id',
        'planner_curriculum_contents_id',
        'units',
        'meetings',
        'trainees',
        'content_type',
        'custom_content',
        'show_custom_content',
        'unit_start_date',
        'unit_end_date',
        'meeting_start_date',
        'meeting_end_date',
        'custom_content_start_date',
        'custom_content_end_date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function plannerWeek(): BelongsTo
    {
        return $this->belongsTo(PlannerWeek::class);
    }

    public function curriculumContent(): BelongsTo
    {
        return $this->belongsTo(PlannerCurriculumContent::class, 'planner_curriculum_contents_id');
    }
}

<?php

namespace App\Models\Planner;

use App\Models\Courses\Unit;
use App\Models\Meetings\Meeting;
use App\Enums\Planner\ContentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PlannerCurriculumContent extends Model
{
    use HasFactory;

    protected $casts = [
        'content_type' => ContentType::class,
        'show_custom_content' => 'boolean',
    ];

    protected $fillable = [
        'planner_curriculum_id',
        'content_type',
        'custom_content',
        'show_custom_content',
        'sort',
    ];

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(PlannerCurriculum::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'planner_curriculum_content_unit');
    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'planner_curriculum_content_meeting');
    }
}

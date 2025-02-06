<?php

namespace App\Models\Exams;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSectionExamTask extends Pivot
{
    use HasFactory;

    protected $table = 'exam_section_task';

    protected $fillable = [
        'exam_task_id',
        'exam_section_id',
    ];

    public function examTask(): BelongsTo
    {
        return $this->belongsTo(ExamTask::class);
    }

    public function examSection(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class);
    }
}
<?php

namespace App\Models\Exams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExamSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'name',
        'short_name',
        'passing_percentage',
    ];

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_exam_section', 'exam_section_id', 'exam_id');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(ExamTask::class, 'exam_section_task');
    }
}
<?php

namespace App\Models\Exams;

use App\Models\Trainee;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProficiencyTrainee extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'proficiency_id',
        'trainee_id',
        'exam_attempt_id',
        'is_proficient',
    ];

    public function proficiency(): BelongsTo
    {
        return $this->belongsTo(Proficiency::class);
    }

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }
}
<?php

namespace App\Models\Exams;

use App\Models\User;
use App\Models\Trainee;
use App\Enums\Exams\ExamAttemptStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $casts = [
        'is_published' => 'boolean',
        'status' => ExamAttemptStatus::class,
    ];

    protected $fillable = [
        'exam_id',
        'trainee_id',
        'instructor_id',
        'date',
        'status',
        'earliest_next_attempt',
        'is_published',
        'feedback',
        'internal_notes',
    ];

    protected static function booted()
    {
        // Marks the trainee as proficient whenever a passing exam attempt is published
        static::saved(function ($examAttempt) {
            if ($examAttempt->is_published) {
                if ($examAttempt->passed) {
                    $examAttempt->exam->proficiency->proficiencyTrainees()->updateOrCreate(
                        ['trainee_id' => $examAttempt->trainee_id],
                        [
                            'is_proficient' => true,
                            'exam_attempt_id' => $examAttempt->id,
                        ]
                    );
                }
            }
        });
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function taskScores(): HasMany
    {
        return $this->hasMany(ExamTaskScore::class);
    }
}
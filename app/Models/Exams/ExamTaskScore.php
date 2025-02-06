<?php

namespace App\Models\Exams;

use App\Models\User;
use App\Models\Trainee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamTaskScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_task_id',
        'trainee_id',
        'instructor_id',
        'exam_attempt_id',
        'score',
    ];

    public function examTask(): BelongsTo
    {
        return $this->belongsTo(ExamTask::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }
}
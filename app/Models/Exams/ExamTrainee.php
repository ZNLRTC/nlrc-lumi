<?php

namespace App\Models\Exams;

use App\Models\Trainee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Enums\Exams\ExamAttemptStatus;
use App\Enums\Exams\ExamTraineeStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ExamTrainee extends Pivot
{
    use HasFactory;

    protected $casts = [
        'status' => ExamTraineeStatus::class,
    ];

    protected $fillable = [
        'exam_id',
        'trainee_id',
        'trainee_alias',
        'internal_notes',
        'exam_location',
        'status',
    ];

    protected static function booted()
    {
        // If the exam attendance status is set to absent and there's no pub, this creates a blank attempt with 0 scores
        static::saving(function ($examTrainee) {

            if ($examTrainee->isDirty('status') && $examTrainee->status === ExamTraineeStatus::ABSENT) {

                $existingAttempt = ExamAttempt::where('exam_id', $examTrainee->exam_id)
                    ->where('trainee_id', $examTrainee->trainee_id)
                    ->first();
                
                if ($existingAttempt) {

                    if (!$existingAttempt->is_published && $existingAttempt->status === ExamAttemptStatus::ABSENT->value) {
                        $existingAttempt->update([
                            'instructor_id' => Auth::id(),
                            'status' => ExamTraineeStatus::ABSENT->value,
                            'feedback' => 'Trainee was absent',
                            'internal_notes' => 'Marked as absent by ' . Auth::user()->name,
                        ]);
                    }
                    
                    return;

                } else {

                    $examAttempt = ExamAttempt::create([
                        'exam_id' => $examTrainee->exam_id,
                        'trainee_id' => $examTrainee->trainee_id,
                        'instructor_id' => Auth::id(),
                        'date' => $examTrainee->exam->date,
                        'is_published' => false,
                        'status' => ExamTraineeStatus::ABSENT->value,
                        'earliest_next_attempt' => null,
                        'feedback' => 'Trainee was absent',
                        'internal_notes' => 'Marked as absent by ' . Auth::user()->name,
                    ]);

                    // Scores to 0 for the absent trainee
                    $sections = $examTrainee->exam->sections()->with('tasks')->get();

                    foreach ($sections as $section) {
                        foreach ($section->tasks as $task) {
                            ExamTaskScore::create([
                                'exam_attempt_id' => $examAttempt->id,
                                'trainee_id' => $examTrainee->trainee_id,
                                'instructor_id' => Auth::id(),
                                'exam_task_id' => $task->id,
                                'score' => 0,
                            ]);
                        }
                    }
                }
            }
        });

        static::saved(function ($examTrainee) {
            if ($examTrainee->wasChanged('status') && $examTrainee->status !== ExamTraineeStatus::ABSENT) {

                $existingAttempt = ExamAttempt::where('exam_id', $examTrainee->exam_id)
                    ->where('trainee_id', $examTrainee->trainee_id)
                    ->where('status', ExamTraineeStatus::ABSENT->value)
                    ->where('is_published', false)
                    ->first();

                if ($existingAttempt) {
                    $existingAttempt->delete();
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
}

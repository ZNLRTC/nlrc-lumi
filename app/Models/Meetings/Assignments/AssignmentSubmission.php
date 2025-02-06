<?php

namespace App\Models\Meetings\Assignments;

use App\Enums\Assignments\SubmissionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Meetings\Assignments\Assignment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $casts = [
        'checked_at' => 'datetime',
        'submission_status' => SubmissionStatus::class,
    ];

    protected $fillable = [
        'assignment_id',
        'trainee_id',
        'instructor_id',
        'submission',
        'attachment_url',
        'feedback',
        'submission_status',
        'checked_at',
        'submitted_at',
        'edited_at',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}

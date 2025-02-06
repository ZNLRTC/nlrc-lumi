<?php

namespace App\Models\Meetings;

use App\Models\User;
use App\Models\Trainee;
use App\Models\Meetings\MeetingStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingTrainee extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'trainee_id',
        'meeting_status_id',
        'instructor_id',
        'internal_notes',
        'feedback',
        'date',
    ];

    public function meetingStatus(): BelongsTo
    {
        return $this->belongsTo(MeetingStatus::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}

<?php

namespace App\Models\Grouping;

use App\Models\User;
use App\Models\Trainee;
use App\Models\Traits\IsActiveTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupTrainee extends Pivot
{
    use IsActiveTrait;

    protected static function booted()
    {
        // These run before the model is saved and are needed so that trainees only have one active group at a time
        static::creating(function ($groupTrainee) {
            static::where('trainee_id', $groupTrainee->trainee_id)
                ->update(['active' => false]);
        });

        static::updating(function ($groupTrainee) {
            if ($groupTrainee->isDirty('active') && $groupTrainee->active) {
                static::where('trainee_id', $groupTrainee->trainee_id)
                    ->where('id', '!=', $groupTrainee->id)
                    ->update(['active' => false]);
            }
        });
    }

    protected $fillable = [
        'trainee_id',
        'group_id',
        'active',
        'notes',
        'added_by',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}

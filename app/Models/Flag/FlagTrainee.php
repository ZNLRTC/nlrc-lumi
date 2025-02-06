<?php

namespace App\Models\Flag;

use App\Models\Trainee;
use App\Models\Traits\IsActiveTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlagTrainee extends Model
{
    use HasFactory;
    use IsActiveTrait;

    protected $casts = [
        'flagged_by_system' => 'boolean',
        'active' => 'boolean',
    ];

    protected $fillable = [
        'trainee_id',
        'flag_id',
        'flagged_by_id',
        'meeting_id',
        'flagged_by_system',
        'active',
        'description',
        'internal_notes',
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function flag(): BelongsTo
    {
        return $this->belongsTo(Flag::class);
    }

    public function flagged_by(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

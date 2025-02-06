<?php

namespace App\Models\Flag;

use App\Models\Trainee;
use App\Models\Traits\IsActiveTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flag extends Model
{
    use HasFactory;
    use IsActiveTrait;

    protected $fillable = [
        'flag_type_id',
        'name',
        'description',
        'visible_to_trainee',
        'active'
    ];

    public function getFlaggedTraineesCountAttribute()
    {
        return $this->belongsToMany(Flag::class, 'flag_trainees')
            ->count();
    }

    public function trainee_flags(): HasMany
    {
        return $this->hasMany(FlagTrainee::class);
    }

    public function trainees(): BelongsToMany
    {
        return $this->belongsToMany(Trainee::class);
    }

    public function flagType(): BelongsTo
    {
        return $this->belongsTo(FlagType::class);
    }
}

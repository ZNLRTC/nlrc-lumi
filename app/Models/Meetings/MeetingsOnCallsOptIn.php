<?php

namespace App\Models\Meetings;

use App\Models\Meetings\MeetingsOnCall;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingsOnCallsOptIn extends Model
{
    protected $fillable = [
        'meetings_on_call_id', 'user_id', 'is_opt_in'
    ];

    public function meetings_on_call(): BelongsTo
    {
        return $this->belongsTo(MeetingsOnCall::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

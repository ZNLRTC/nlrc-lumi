<?php

namespace App\Models\Meetings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'internal_name',
        'description',
    ];

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }
}

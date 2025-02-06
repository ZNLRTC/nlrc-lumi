<?php

namespace App\Models;

use App\Models\Trainee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    public function traineesResidence(): HasMany
    {
        return $this->hasMany(Trainee::class, 'country_of_residence_id');
    }

    public function traineesNationality(): HasMany
    {
        return $this->hasMany(Trainee::class, 'country_of_citizenship_id');
    }
}

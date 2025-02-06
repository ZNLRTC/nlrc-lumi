<?php

namespace App\Models\Exams;

use App\Models\Grouping\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Proficiency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function proficiencyTrainees(): HasMany
    {
        return $this->hasMany(ProficiencyTrainee::class);
    }

}
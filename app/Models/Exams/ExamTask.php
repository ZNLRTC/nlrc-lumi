<?php

namespace App\Models\Exams;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExamTask extends Model
{
    use HasFactory;

    protected $casts = [
        'mandatory_to_pass' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'max_score',
        'min_score',
        'passing_score',
        'mandatory_to_pass',
    ];

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(ExamSection::class, 'exam_section_task');
    }

    public function examTaskScores(): HasMany
    {
        return $this->hasMany(ExamTaskScore::class);
    }
}
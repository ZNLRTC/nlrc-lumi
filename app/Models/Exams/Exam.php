<?php

namespace App\Models\Exams;

use App\Models\Trainee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'type',
        'any_instructor_can_grade',
        'allowed_instructors',
        'exam_locations',
        'exam_paper_url',
        'proficiency_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'allowed_instructors' => 'array',
        'exam_locations' => 'array',
        'any_instructor_can_grade' => 'boolean',
    ];

    public function proficiency(): BelongsTo
    {
        return $this->belongsTo(Proficiency::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(ExamSection::class, 'exam_exam_section', 'exam_id', 'exam_section_id');
    }

    public function trainees(): BelongsToMany
    {
        return $this->belongsToMany(Trainee::class, 'exam_trainee', 'exam_id', 'trainee_id')
            ->withPivot('trainee_alias', 'internal_notes', 'exam_location', 'status')
            ->using(ExamTrainee::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
    
}
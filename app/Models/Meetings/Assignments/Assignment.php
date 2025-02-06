<?php

namespace App\Models\Meetings\Assignments;

use App\Models\Courses\Unit;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Assignments\AttachmentType;
use App\Enums\Assignments\SubmissionType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Meetings\Assignments\AssignmentSubmission;

class Assignment extends Model
{
    use HasFactory;

    protected $casts = [
        'submission_type' => SubmissionType::class,
        'attachment_type' => AttachmentType::class,
    ];

    protected $fillable = [
        'unit_id',
        'name',
        'internal_name',
        'description',
        'internal_notes',
        'submission_type',
        'attachment_type',
        'slug',
    ];

    // Units are cached with assignments eager loaded so the cache should be cleared when an assignment is updated
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($assignment) {
            $assignment->unit->clearCache();
        });

        static::created(function ($assignment) {
            $assignment->unit->clearCache();
        });

        static::deleted(function ($assignment) {
            $assignment->unit->clearCache();
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
    
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

}

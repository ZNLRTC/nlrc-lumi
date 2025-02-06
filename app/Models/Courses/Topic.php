<?php

namespace App\Models\Courses;

use App\Models\Courses\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'title',
        'description',
        'slug',
        'sort',
        'content',
    ];

    protected static function boot()
    {
        parent::boot();

        // Units are cached with topics eager loaded so the cache should be cleared when a topic is updated
        static::updated(function ($topic) {
            $topic->unit->clearCache();
        });

        static::created(function ($topic) {
            $topic->unit->clearCache();
        });

        static::deleted(function ($topic) {
            $topic->unit->clearCache();
        });
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}

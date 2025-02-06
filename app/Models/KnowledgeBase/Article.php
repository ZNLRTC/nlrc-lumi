<?php

namespace App\Models\KnowledgeBase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\KnowledgeBase\ArticleStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $casts = [
        'audiences' => 'array',
        'last_reset_at' => 'datetime',
        'status' => ArticleStatus::class,
    ];

    protected $table = 'kb_articles';

    protected $fillable = [
        'category_id',
        'title',
        'content',
        'summary',
        'status',
        'slug',
        'audiences',
        'view_count',
        'helpful_count',
        'not_helpful_count',
        'last_reset_at',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->where('status', ArticleStatus::PUBLISHED);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}

<?php

namespace App\Models\KnowledgeBase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kb_categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}

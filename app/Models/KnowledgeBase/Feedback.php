<?php

namespace App\Models\KnowledgeBase;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'kb_feedback';

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected $fillable = [
        'article_id',
        'user_id',
        'feedback',
        'is_read',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models\Documents;

use App\Models\Trainee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'internal_name',
        'description',
        'internal_notes',
    ];

    public function getDocumentCountAttribute()
    {
        return $this->belongsToMany(Trainee::class, 'document_trainees')
            ->count();
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Trainee::class, 'document_trainees')
            ->withPivot('url', 'status', 'comments', 'internal_notes')
            ->withTimestamps();
    }
}

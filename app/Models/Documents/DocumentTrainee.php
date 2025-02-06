<?php

namespace App\Models\Documents;

use App\Enums\DocumentTraineesStatus;
use App\Models\Trainee;
use App\Models\Documents\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentTrainee extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'trainee_id',
        'url',
        'status',
        'comments',
        'internal_notes',
    ];

    protected $casts = ['status' => DocumentTraineesStatus::class];

    protected static function booted()
    {
        static::deleting(function ($documentTrainee) {

            Storage::disk('documents')->delete($documentTrainee->url);

        });
    }

    public function scopeSubmittedThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            date('Y-m-d', strtotime('first day of this month')), date('Y-m-d', strtotime('last day of this month'))
        ]);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}

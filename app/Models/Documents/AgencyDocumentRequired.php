<?php

namespace App\Models\Documents;

use App\Models\Agencies\Agency;
use App\Models\Documents\Document;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyDocumentRequired extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'document_id',
        'is_required',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function getDocumentCountForAgencyAttribute(): int
    {
        return $this->hasMany(DocumentTrainee::class, 'document_id', 'document_id')
            ->whereHas('trainee', fn (Builder $query) => $query->where('agency_id', $this->agency_id))
            ->count();
    }
}

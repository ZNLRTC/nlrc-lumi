<?php

namespace App\Models\Documents;

use App\Models\Trainee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTraineeOverride extends Model
{
    use HasFactory;

    protected $table = 'document_trainee_override';

    protected $fillable = [
        'document_trainee_id',
        'trainee_id',
        'is_required',
    ];

    public function documentTrainee(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }
}

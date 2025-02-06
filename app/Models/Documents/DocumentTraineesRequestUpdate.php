<?php

namespace App\Models\Documents;

use App\Enums\DocumentTraineesRequestUpdatesApprovalStatus;
use App\Models\Documents\DocumentTrainee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class DocumentTraineesRequestUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_trainee_id',
        'staff_user_id', // This is not a foreign key since this is nullable on create
        'reason',
        'approval_status'
    ];

    protected $casts = ['approval_status' => DocumentTraineesRequestUpdatesApprovalStatus::class];

    public function document_trainee(): BelongsTo
    {
        return $this->belongsTo(DocumentTrainee::class);
    }
}

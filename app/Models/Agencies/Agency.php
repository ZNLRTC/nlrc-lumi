<?php

namespace App\Models\Agencies;

use App\Models\Documents\AgencyDocumentRequired;
use App\Models\Observer;
use App\Models\Trainee;
use App\Models\Traits\IsActiveTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Agency extends Model
{
    use HasFactory;
    use IsActiveTrait;

    protected $fillable = [
        'name',
        'description',
    ];

    public function observer(): HasOne
    {
        return $this->hasOne(Observer::class);
    }

    public function trainees(): HasMany
    {
        return $this->hasMany(Trainee::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AgencyDocumentRequired::class, 'agency_id');
    }

    public function getDocumentsCountAttribute(): int
    {
        return $this->documents()->count();
    }

    // Custom model functions
    protected function get_agency_id_by_group_name($group_name): int
    {
        if (
            str_contains($group_name, 'ATT') ||
            str_contains($group_name, 'HKI') ||
            str_contains($group_name, 'SUOM') ||
            str_contains($group_name, 'SUO')
        ) {
            $agency_id = 1;
        } else if (str_contains($group_name, 'FIN')) {
            $agency_id = 2;
        } else if (str_contains($group_name, 'KEN')) {
            $agency_id = 3;
        } else if (str_contains($group_name, 'INTD')) {
            $agency_id = 4;
        } else if (str_contains($group_name, 'INTW')) {
            $agency_id = 5;
        } else {
            $agency_id = 6;
        }

        return $agency_id;
    }
}

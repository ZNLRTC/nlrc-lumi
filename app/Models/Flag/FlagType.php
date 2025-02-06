<?php

namespace App\Models\Flag;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlagType extends Model
{
    use HasFactory;

    public function statuses(): HasMany
    {
        return $this->hasMany(Flag::class);
    }
}

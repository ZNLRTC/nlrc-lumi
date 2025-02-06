<?php

namespace App\Models\Grouping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'code', 'description'
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}

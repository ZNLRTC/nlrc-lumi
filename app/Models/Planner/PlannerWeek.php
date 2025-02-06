<?php

namespace App\Models\Planner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlannerWeek extends Model
{
    use HasFactory;

    protected $casts = [
        'finalized' => 'boolean',
    ];

    protected $fillable = [
        'number',
        'year',
        'start_date',
        'end_date',
        'finalized',
    ];

    public function weeklySchedules()
    {
        return $this->hasMany(PlannerWeeklySchedule::class);
    }
}

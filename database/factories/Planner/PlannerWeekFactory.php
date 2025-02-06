<?php

namespace Database\Factories\Planner;

use Illuminate\Support\Carbon;
use App\Models\Planner\PlannerWeek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Planner\PlannerWeek>
 */
class PlannerWeekFactory extends Factory
{
    protected $model = PlannerWeek::class;
    
    public function definition(): array
    {
        $currentDate = Carbon::now();
        $weekStartDate = $currentDate->startOfWeek(Carbon::MONDAY);
        $weekEndDate = $currentDate->endOfWeek(Carbon::SUNDAY);

        return [
            'number' => $weekStartDate->isoWeek(),
            'year' => $weekStartDate->year,
            'start_date' => $weekStartDate,
            'end_date' => $weekEndDate,
        ];
    }
}

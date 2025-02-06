<?php

namespace Database\Factories\Meetings;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meetings\MeetingTrainee>
 */
class MeetingTraineeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => $this->faker->numberBetween(1, 28),
            'trainee_id' => $this->faker->numberBetween(1),
            'meeting_status_id' => $this->faker->numberBetween(1, 3),
            'instructor_id' => $this->faker->numberBetween(5, 7),
            'internal_notes' => $this->faker->randomElement(['Poor Internet connection', 'Uses automatic translation', 'Seems to have issues with English','','','']),
            'feedback' => $this->faker->sentence,
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        ];
    }
}

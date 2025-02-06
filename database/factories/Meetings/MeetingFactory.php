<?php

namespace Database\Factories\Meetings;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_id' => $this->faker->numberBetween(1, 28),
            'meeting_type_id' => 1,
            'description' => '1:1 about unit ' . $this->faker->numberBetween(1, 28),
        ];
    }
}

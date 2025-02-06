<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trainee>
 */
class TraineeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sex = fake()->randomElement(['female', 'male']);

        return [
            'active' => true,
            'first_name' => fake()->firstName($sex),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->date(),
            'address' => fake()->address(),
            'sex' => $sex,
            'phone_number' => fake()->phoneNumber(),
        ];
    }
}

<?php

namespace Database\Factories\Courses;

use App\Models\Courses\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'internal_name' => $this->faker->sentence,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->paragraph,
            'created_at' => now(),
        ];
    }
}

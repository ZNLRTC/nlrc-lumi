<?php

namespace Database\Factories\Courses;

use Illuminate\Support\Str;
use App\Models\Courses\Unit;
use App\Models\Courses\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $uniqueWord = $this->faker->unique()->word;

        static $sort = 1;

        $startingSentences = [
            'In this unit, you will learn',
            'This unit covers',
            'This unit will teach you',
            'This unit is about',
            'This unit discusses',
        ];

        $startingSentence = $this->faker->randomElement($startingSentences);

        $randomWords = $this->faker->words(5, true);

        $description = $startingSentence . ' ' . $randomWords . '.';

        return [
            'course_id' => Course::factory(),
            'name' => $uniqueWord,
            'internal_name' => $uniqueWord,
            'sort' => $sort++,
            'slug' => Str::slug($uniqueWord),
            'description' => $description,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Unit $unit) {
            $unit->name = 'Unit ' . $unit->id;
            $unit->internal_name = 'Unit ' . $unit->id . ' (main)';
            $unit->slug = 'unit-' . $unit->id;
            $unit->save();
        });
    }
}
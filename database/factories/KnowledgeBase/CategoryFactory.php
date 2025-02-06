<?php

namespace Database\Factories\KnowledgeBase;

use App\Models\KnowledgeBase\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KnowledgeBase\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'slug' => $this->faker->unique()->slug,
        ];
    }
}

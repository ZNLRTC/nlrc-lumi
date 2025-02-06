<?php

namespace Database\Factories\Courses;

use Illuminate\Support\Str;
use App\Models\Courses\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\odel:Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $title = $this->faker->sentence;

        // Random stuff as content
        $content = '';
        for ($i = 0; $i < rand(3, 6); $i++) {
            $content .= '<p>' . $this->faker->paragraph(rand(3, 6)) . '</p>';
            if (rand(0, 1)) {
                $content .= '<ul><li>' . implode('</li><li>', $this->faker->words(rand(2, 5))) . '</li></ul>';
            }
        }

        return [
            'unit_id' => Unit::factory(),
            'title' => $title,
            'slug' => Str::slug($title), // Dashes instead of spaces
            'description' => $this->faker->paragraph,
            'sort' => 1,
            'content' => $content,
        ];
    }
}

<?php

namespace Database\Factories\KnowledgeBase;

use App\Models\KnowledgeBase\Article;
use App\Models\KnowledgeBase\Category;
use App\Enums\KnowledgeBase\ArticleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KnowledgeBase\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => $this->faker->sentence(4),
            'summary' => $this->faker->sentences(2, true),
            'content' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->boolean(90) ? ArticleStatus::PUBLISHED : ArticleStatus::DRAFT,
            'slug' => $this->faker->unique()->slug,
            'audiences' => $this->faker->randomElements(['Trainee', 'Instructor', 'Observer'], null),
        ];
    }
}

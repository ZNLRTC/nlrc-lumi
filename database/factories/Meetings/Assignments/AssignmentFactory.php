<?php

namespace Database\Factories\Meetings\Assignments;

use Illuminate\Support\Str;
use App\Models\Courses\Unit;
use App\Enums\Assignments\AttachmentType;
use App\Enums\Assignments\SubmissionType;
use App\Models\Meetings\Assignments\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition()
    {
        return [
            'unit_id' => Unit::factory(),
            'name' => $this->faker->sentence,
            'internal_name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'internal_notes' => $this->faker->paragraph,
            'submission_type' => SubmissionType::TEXT,
            'attachment_type' => AttachmentType::IMAGE,
            'slug' => Str::slug($this->faker->unique()->sentence),
        ];
    }
}

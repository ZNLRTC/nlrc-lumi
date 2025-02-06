<?php

namespace Database\Factories\Meetings\Assignments;

use App\Enums\Assignments\SubmissionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssignmentSubmission>
 */
class AssignmentSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isChecked = $this->faker->boolean(40);
        $failed = $this->faker->boolean(10);
        $isEdited = $this->faker->boolean(20);

        $submissionStatus = SubmissionStatus::NOT_CHECKED;
        $feedback = null;
        $instructorId = null;
        $checkedAt = null;
        $editedAt = null;

        if ($isChecked) {
            $submissionStatus = SubmissionStatus::COMPLETED;
            $instructorId = $this->faker->randomElement([5, 6, 7]);
            $feedback = $this->faker->randomElement(['Good job!', 'Hyvä! Great work!', 'Keep up the goob work!']);
            $checkedAt = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
        }

        if ($failed) {
            $submissionStatus = SubmissionStatus::INCOMPLETE;
            $instructorId = $this->faker->randomElement([5, 6, 7]);
            $feedback = $this->faker->text(200);
            $checkedAt = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
        }

        if ($isEdited) {
            $editedAt = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
        }

        return [
            'assignment_id' => $this->faker->randomNumber(1, 28),
            'trainee_id' => User::factory(),
            'instructor_id' => $instructorId,
            'submission' => $this->faker->text(200),
            'attachment_url' => null,
            'feedback' => $feedback,
            'submission_status' => $submissionStatus,
            'checked_at' => $checkedAt,
            'submitted_at' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'edited_at' => $editedAt,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

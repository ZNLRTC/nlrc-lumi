<?php

use Livewire\Livewire;
use Illuminate\Support\Facades\DB;
use App\Enums\Assignments\SubmissionStatus;
use App\Models\Meetings\Assignments\Assignment;
use App\Models\Meetings\Assignments\AssignmentSubmission;

describe('Assignments', function() {

    function createAssignment()
    {
        return Assignment::factory()->create();
    }
    
    function getSubmissionPageResponse($test, $user, $assignment)
    {
        return $test->actingAs($user)->get(route('assignments.create', [
            'course' => $assignment->unit->course->slug,
            'unit' => $assignment->unit->slug,
            'assignment' => $assignment->slug,
        ]));
    }
    
    test('active trainee can access the submission page', function () {
        $trainee = createTrainee();
        $assignment = createAssignment();
    
        // There's middleware that prevents trainee's access if the group has no access to the course
        DB::table('course_group')->insert([
            'course_id' => $assignment->unit->course->id,
            'group_id' => $trainee->trainee->activeGroup->group->id,
        ]);
    
        getSubmissionPageResponse($this, $trainee, $assignment)
            ->assertSuccessful();
    });
    
    test('an inactive trainee is redirected to dashboard when accessing the submission page', function () {
        $trainee = createInactiveTrainee();
        $assignment = createAssignment();
    
        getSubmissionPageResponse($this, $trainee, $assignment)
            ->assertStatus(302)
            ->assertRedirect(route('dashboard'));
    });
    
    test('instructor is redirected to dashboard when accessing the submission page', function () {
        $instructor = createInstructor();
        $assignment = createAssignment();
    
        getSubmissionPageResponse($this, $instructor, $assignment)
            ->assertStatus(302)
            ->assertRedirect(route('dashboard'));
    });
    
    it('can find the component', function () {
        $trainee = createTrainee();
        $assignment = createAssignment();
    
        Livewire::actingAs($trainee)
            ->test('meetings.assignments.submit-assignment', ['assignment' => $assignment])
            ->assertSuccessful();
    });
    
    it('allows a trainee to submit an assignment', function () {
        $trainee = createTrainee();
        $assignment = createAssignment();
    
        Livewire::actingAs($trainee)
            ->test('meetings.assignments.submit-assignment', ['assignment' => $assignment])
            ->set('submission', 'Google Translate did my homework.')
            ->call('submit')
            ->assertHasNoErrors();
    
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'trainee_id' => $trainee->trainee->id,
            'submission' => 'Google Translate did my homework.',
            'submission_status' => SubmissionStatus::NOT_CHECKED,
        ]);
    });
    
    it('allows a trainee to start editing a submission', function () {
        $trainee = createTrainee();
        $assignment = createAssignment();
    
        // Submission status needs to specified here because the factory pumps out NOT_CHECKED, COMPLETED, and INCOMPLETE submissions at random
        AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->id,
            'instructor_id' => null,
            'trainee_id' => $trainee->trainee->id,
            'submission' => 'Totally my own work.',
            'submission_status' => SubmissionStatus::NOT_CHECKED,
        ]);
    
        Livewire::actingAs($trainee)
            ->test('meetings.assignments.submit-assignment', ['assignment' => $assignment])
            ->call('startEditing')
            ->assertSet('isEditing', true);
    });
    
    it('allows a trainee to cancel editing a submission', function () {
        $trainee = createTrainee();
        $assignment = createAssignment();
    
        $submission = AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->id,
            'instructor_id' => null,
            'trainee_id' => $trainee->trainee->id,
            'submission' => 'Initial submission.',
            'submission_status' => SubmissionStatus::NOT_CHECKED,
        ]);
    
        Livewire::actingAs($trainee)
            ->test('meetings.assignments.submit-assignment', ['assignment' => $assignment])
            ->call('startEditing')
            ->set('submission', 'Edited submission.')
            ->call('cancelEditing')
            ->assertSet('submission', $submission->submission)
            ->assertSet('isEditing', false);
    });
    
    it('allows a trainee to update their own submission', function () {
        $trainee = createTrainee();
        $assignment = createAssignment();
    
        $submission = AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->id,
            'instructor_id' => null,
            'trainee_id' => $trainee->trainee->id,
            'submission' => 'Initial submission.',
            'submission_status' => SubmissionStatus::NOT_CHECKED,
        ]);
    
        Livewire::actingAs($trainee)
            ->test('meetings.assignments.submit-assignment', ['assignment' => $assignment])
            ->call('startEditing')
            ->set('submission', 'Edited submission.')
            ->call('updateSubmission')
            ->assertSet('isEditing', false);
    
        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'assignment_id' => $assignment->id,
            'instructor_id' => null,
            'trainee_id' => $trainee->trainee->id,
            'submission' => 'Edited submission.',
        ]);
    });
    
    it('prevents a trainee from editing someone else\'s submission via dev tools', function () {
        $underPerformingTrainee = createTrainee();
        $hackingTrainee = createTrainee();
        $assignment = createAssignment();
    
        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'trainee_id' => $underPerformingTrainee->trainee->id,
            'submission' => 'I did not study so I wrote total crap.',
            'submission_status' => SubmissionStatus::NOT_CHECKED,
            'submitted_at' => now(),
        ]);
    
        Livewire::actingAs($hackingTrainee)
            ->test('meetings.assignments.submit-assignment', ['assignment' => $assignment])
            ->call('startEditing')
            ->set('submission', 'Perfect Finnish submission for my best friend.')
            ->call('updateSubmission')
            ->assertForbidden();
    
        $this->assertDatabaseMissing('assignment_submissions', [
            'id' => $submission->id,
            'submission' => 'Perfect Finnish submission for my best friend.',
        ]);
    });
    
    it('prevents editing if assignment was graded', function () {
        $trainee = createTrainee();
        $instructor = createInstructor();
        $assignment = createAssignment();
    
        AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->id,
            'instructor_id' => $instructor->id,
            'trainee_id' => $trainee->trainee->id,
            'submission' => 'This should not change.',
            'submission_status' => SubmissionStatus::COMPLETED,
        ]);
    
        Livewire::actingAs($trainee)
            ->test('meetings.assignments.submit-assignment', ['assignment' => $assignment])
            ->call('startEditing')
            ->set('submission', 'This should not be in DB.')
            ->call('updateSubmission')
            ->assertSet('isEditing', false);
    
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'instructor_id' => $instructor->id,
            'trainee_id' => $trainee->trainee->id,
            'submission' => 'This should not change.',
        ]);
    });
})->group('assignments');
<?php

use App\Models\Courses\Unit;
use App\Livewire\Course\Main;
use App\Models\Courses\Course;
use App\Models\Grouping\Group;
use App\Livewire\Course\ShowUnit;
use Illuminate\Support\Facades\DB;
use App\Livewire\Meetings\UnitMeetings;
use App\Models\Meetings\Assignments\Assignment;

describe('Course access', function() {

    test('active trainee can access the KMH course and its unit routes', function() {
        $trainee = createTrainee();
    
        $KMH = Course::factory()->create(['name' => 'Kyl mä hoidan', 'slug' => 'kmh']);
        $KMHUnit = Unit::factory()->create(['course_id' => $KMH->id]);
    
        $this->actingAs($trainee)
            ->get(route('courses.kmh'))
            ->assertSee($KMH->name)
            ->assertSeeLivewire(Main::class)
            ->assertSuccessful();
    
        $this->actingAs($trainee)
            ->get(route('units.index', ['course' => $KMH->slug, 'unit' => $KMHUnit->slug]))
            ->assertSeeLivewire(ShowUnit::class)
            ->assertSuccessful();
    });
    
    test('active trainee can access a non-KMH course and its unit routes', function() {
        $trainee = createTrainee();
    
        $course = Course::factory()->create();
        $unit = Unit::factory()->create(['course_id' => $course->id]);
    
        DB::table('course_group')->insert([
            'course_id' => $course->id,
            'group_id' => $trainee->trainee->activeGroup->group->id,
        ]);
    
        $this->actingAs($trainee)
            ->get(route('courses.index', ['course' => $course->slug]))
            ->assertSeeLivewire(Main::class)
            ->assertSuccessful();
    
        $this->actingAs($trainee)
            ->get(route('units.index', ['course' => $course->slug, 'unit' => $unit->slug]))
            ->assertSeeLivewire(ShowUnit::class)
            ->assertSeeLivewire(UnitMeetings::class)
            ->assertSuccessful();
    });
    
    test('inactive trainee can only access KMH and see its Livewire components', function() {
        $inactiveTrainee = createInactiveTrainee();
    
        $KMH = Course::factory()->create(['name' => 'Kyl mä hoidan', 'slug' => 'kmh']);
        $notKMH = Course::factory()->create();
    
        $this->actingAs($inactiveTrainee)
            ->get(route('courses.kmh'))
            ->assertSee($KMH->name)
            ->assertSeeLivewire(Main::class)
            ->assertSuccessful();
    
        $unit = Unit::factory()->create(['course_id' => $notKMH->id]);
        $notThisTraineesGroup = Group::factory()->create(['id' => 999]);
    
        DB::table('course_group')->insert([
            'course_id' => $notKMH->id,
            'group_id' => $notThisTraineesGroup->id,
        ]);
    
        $this->actingAs($inactiveTrainee)
            ->get(route('courses.index', ['course' => $notKMH->slug]))
            ->assertDontSeeLivewire(Main::class)
            ->assertStatus(302)
            ->assertRedirect(route('dashboard'));
    
        $this->actingAs($inactiveTrainee)->get(route('units.index', ['course' => $notKMH->slug, 'unit' => $unit->slug]))
            ->assertDontSeeLivewire(ShowUnit::class)
            ->assertDontSeeLivewire(UnitMeetings::class)
            ->assertStatus(302)
            ->assertRedirect(route('dashboard'));
    });
    
    test('instructor can access all courses', function() {
        $instructor = createInstructor();
    
        Course::factory()->create(['name' => 'KMH', 'slug' => 'kmh']);
        $notKMH = Course::factory()->create();
        $unit = Unit::factory()->create(['course_id' => $notKMH->id]);
    
        $this->actingAs($instructor)
            ->get(route('courses.kmh'))
            ->assertSuccessful()
            ->assertSeeLivewire(Main::class);
    
        $this->actingAs($instructor)
            ->get(route('courses.index', ['course' => $notKMH->slug]))
            ->assertSuccessful()
            ->assertSeeLivewire(Main::class);
    
        $this->actingAs($instructor)
            ->get(route('units.index', ['course' => $notKMH->slug, 'unit' => $unit->slug]))
            ->assertSuccessful()
            ->assertSeeLivewire(ShowUnit::class);
    });
    
    it('shows assignment submission links and meetings to active trainees', function() {
        $trainee = createTrainee();
        $assignment = Assignment::factory()->create();
    
        DB::table('course_group')->insert([
            'course_id' => $assignment->unit->course->id,
            'group_id' => $trainee->trainee->activeGroup->group->id,
        ]);
    
        $this->actingAs($trainee)
            ->get(route('units.index', ['course' => $assignment->unit->course->slug, 'unit' => $assignment->unit->slug]))
            ->assertSeeLivewire(UnitMeetings::class);
    });
    
    it('does not show assignments or meetings to users in the KMH course', function() {
        $trainee = createTrainee();
    
        $KMH = Course::factory()->create(['name' => 'Kyl mä hoidan', 'slug' => 'kmh']);
        $KMHUnit = Unit::factory()->create(['course_id' => $KMH->id]);
        Assignment::factory()->create(['unit_id' => $KMHUnit->id]);
    
        $this->actingAs($trainee)
            ->get(route('units.index', ['course' => $KMH->slug, 'unit' => $KMHUnit->slug]))
            ->assertDontSee('Assignments in this unit')
            ->assertDontSeeLivewire(UnitMeetings::class);
    });
    
    
    it('does not show assignment submission links or meeting lists to instructors', function() {
        $instructor = createInstructor();
        $assignment = Assignment::factory()->create();
    
        $this->actingAs($instructor)->get(route('units.index', [
            'course' => $assignment->unit->course->slug,
            'unit' => $assignment->unit->slug,
        ]))
            ->assertDontSee('Assignments in this unit')
            ->assertDontSeeLivewire(UnitMeetings::class);
    });
})->group('course-access');;
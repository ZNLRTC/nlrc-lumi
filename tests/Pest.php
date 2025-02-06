<?php

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Grouping\Group;
use App\Models\Grouping\GroupTrainee;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

// This assumes that the trainee is a member of a group and the group has access to their course
function createTrainee()
{
    $traineeRole = Role::firstOrCreate(['name' => 'Trainee'], ['description' => 'A trainee!']);
    $group = Group::factory()->create();
   
    // This disables the model event that would otherwise create a trainee and always add them to a course with id == 1 (KMH)
    Event::fakeFor(function () use ($traineeRole, $group, &$trainee) {
        $trainee = User::factory()->create(['role_id' => $traineeRole->id]);
        $trainee->trainee()->create(['group_id' => $group->id]);

        $groupTrainee = new GroupTrainee();
        $groupTrainee->trainee_id = $trainee->trainee->id;
        $groupTrainee->group_id = $group->id;
        $groupTrainee->notes = 'Test placement';
        $groupTrainee->active = true;
        $groupTrainee->save();
    });

    return $trainee;
}

function createInactiveTrainee()
{
    $traineeRole = Role::firstOrCreate(['name' => 'Trainee'], ['description' => 'An inactive trainee!']);
    $group = Group::factory()->create();
    
    Event::fakeFor(function () use ($traineeRole, $group, &$trainee) {
        $trainee = User::factory()->create(['role_id' => $traineeRole->id]);
        $trainee->trainee()->create(['group_id' => $group->id]);

        $groupTrainee = new GroupTrainee();
        $groupTrainee->trainee_id = $trainee->trainee->id;
        $groupTrainee->group_id = $group->id;
        $groupTrainee->notes = 'Test placement';
        $groupTrainee->active = false;
        $groupTrainee->save();
    });

    $trainee->trainee->update(['active' => false]);
    // dd($trainee->trainee->activeGroup->group->first());

    return $trainee;
}

function createUserWithRole($roleName, $roleDescription, $domain = 'nlrc.ph')
{
    $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleDescription]);
    return User::factory()->create([
        'role_id' => $role->id,
        'email' => fake()->unique()->userName . '@' . $domain, // Filament route tests fail if the domain is not 'nlrc.ph' or 'nlrc.fi'
    ]);
}

function createInstructor($domain = 'nlrc.fi')
{
    return createUserWithRole('Instructor', 'An instructor!', $domain);
}

function createStaff($domain = 'nlrc.ph')
{
    return createUserWithRole('Staff', 'A staff member!', $domain);
}

function createObserver()
{
    return createUserWithRole('Observer', 'Probably working for a competitor...');
}

function createManager($domain = 'nlrc.ph')
{
    return createUserWithRole('Manager', 'Leaders-by-example!', $domain);
}

function createAdmin($domain = 'nlrc.ph')
{
    return createUserWithRole('Admin', 'The CRUD Commander!', $domain);
}
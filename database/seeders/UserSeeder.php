<?php

namespace Database\Seeders;

use App\Models\Agencies\Agency;
use App\Models\Meetings\Assignments\AssignmentSubmission;
use App\Models\Meetings\MeetingTrainee;
use App\Models\Observer;
use App\Models\Trainee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    use WithoutModelEvents;

    public function run(): void
    {
        $default_pass = Hash::make('password');

        // Comment this out if you wanna use the UserFactory after the initial seeding
        User::insert([
            // 1 – 5 are admins
            //['name' => 'Mikko', 'email' => 'mikko@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 1, 'timezone' => 'Asia/Manila' ],
            //['name' => 'Jushua', 'email' => 'jushua@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 1, 'timezone' => 'Asia/Manila' ],
            //['name' => 'Karl', 'email' => 'karl@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 1, 'timezone' => 'Asia/Manila' ],
            ['name' => 'Migs', 'email' => 'migs@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 1, 'timezone' => 'Asia/Manila' ],
            ['name' => 'Kevin', 'email' => 'storm_iz_here@yahoo.com', 'password' => $default_pass, 'restricted' => false, 'role_id' => 1, 'timezone' => 'Asia/Manila' ],
            // 6 – 8 are instructors
            // ['name' => 'NLRC instructor 1', 'email' => 'instructor1@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 5, 'timezone' => 'Europe/Helsinki' ],
            // ['name' => 'NLRC instructor 2', 'email' => 'instructor2@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 5, 'timezone' => 'Europe/Helsinki'],
            // ['name' => 'NLRC instructor 3', 'email' => 'instructor3@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 5, 'timezone' => 'Europe/Helsinki'],
            // 9 – 11 are staff
            // Change the indexes for staff in random $staffId below if you add users above this line
            // ['name' => 'Staff member 1', 'email' => 'staff1@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 2, 'timezone' => 'Asia/Manila' ],
            // ['name' => 'Staff member 2', 'email' => 'staff2@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 2, 'timezone' => 'Asia/Manila' ],
            // ['name' => 'Staff member 3', 'email' => 'staff3@nlrc.ph', 'password' => $default_pass, 'restricted' => false, 'role_id' => 2, 'timezone' => 'Asia/Manila' ],
        ]);

        // Observers
        User::factory()
            ->count(5)
            ->observers()
            ->unverified()
            ->state(new Sequence(
                ['password' => $default_pass, 'notification_settings' => null],
                ['password' => $default_pass, 'notification_settings' => null],
                ['password' => $default_pass, 'notification_settings' => null],
                ['password' => $default_pass, 'notification_settings' => null],
                ['password' => $default_pass, 'notification_settings' => null]
            ))
            ->create()
            ->each(function($user) {
                if (str_contains($user->email, 'silkroad')) {
                    $agency_id = 1;
                } else if (str_contains($user->email, 'topmake')) {
                    $agency_id = 2;
                } else if (str_contains($user->email, 'africaworkpower')) {
                    $agency_id = 3;
                } else if (str_contains($user->email, 'nsdcinternational')) {
                    $agency_id = 4;
                } else if (str_contains($user->email, 'walsonshealthcare')) {
                    $agency_id = 5;
                }

                Observer::create([
                    'user_id' => $user->id,
                    'agency_id' => $agency_id
                ]);
            });

        User::factory(100)
            ->create()
            ->each(function ($user, $index) {
                $trainee = Trainee::factory()->make();
                $user->trainee()->save($trainee);

                $kmhGroupId = 1;

                $trainee->group()->attach($kmhGroupId, [
                    'active' => false,
                    'notes' => 'Assigned to beginners course.',
                    'created_at' => Carbon::now()->subMonths(4)
                ]);

                $randomGroupId = rand(2, 25);
                $randomStaffId = rand(9, 11);

                $trainee->group()->attach($randomGroupId, [
                    'active' => $index % 5 >= 2, // 60% have active=>true
                    'notes' => 'Initial group assignment.',
                    'added_by' => $randomStaffId,
                    'created_at' => Carbon::now()->subMonths(1),
                ]);

                if ($index % 5 < 2) { // 40% have two groups
                    $anotherRandomGroupId = rand(2, 25);
                    $anotherRandomStaffId = rand(9, 11);

                    while ($anotherRandomGroupId == $randomGroupId) { // Place in differnet groups
                        $anotherRandomGroupId = rand(2, 25);
                    }

                    $trainee->group()->attach($anotherRandomGroupId, [
                        'active' => true,
                        'notes' => 'Too much behind in their studies.',
                        'added_by' => $anotherRandomStaffId,
                        'created_at' => Carbon::now()->subWeek(),
                    ]);
                }

                $randomCountryId = rand(1, 190);
                $trainee->countryOfCitizenship()->associate($randomCountryId);
                $trainee->countryOfResidence()->associate($randomCountryId);

                $agency_id = Agency::get_agency_id_by_group_name($trainee->activeGroup->group->group_code);
                $trainee->agency()->associate($agency_id);

                // Add random meetings and an assignment for each
                $meetingTrainees = MeetingTrainee::factory()->count(rand(5, 25))->create(['trainee_id' => $trainee->id]);

                foreach ($meetingTrainees as $meetingTrainee) {
                    AssignmentSubmission::factory()->create([
                        'trainee_id' => $trainee->id,
                        'assignment_id' => $meetingTrainee->meeting_id,
                    ]);
                }

                // Add random exams, skipping exams 5–7 cos they're templates
                $examIds = array_merge(range(1, 4), range(8, 11));
                shuffle($examIds);
                $randomExamIds = array_slice($examIds, 0, rand(0, 3));
                foreach ($randomExamIds as $examId) {
                    $trainee->exams()->attach($examId);
                }

                $trainee->save();
        });

        User::factory(40)
            ->create()
            ->each(function ($user) {
                $trainee = Trainee::factory()->make();
                $user->trainee()->save($trainee);

                $randomGroupId = 1;

                $trainee->group()->attach($randomGroupId, [
                    'active' => true,
                    'notes' => 'Assigned to beginners course.',
                    'created_at' => Carbon::now()->subMonths(2),
                ]);

                $randomCountryId = rand(1, 190);
                $trainee->countryOfCitizenship()->associate($randomCountryId);
                $trainee->countryOfResidence()->associate($randomCountryId);

                $agency_id = Agency::get_agency_id_by_group_name($trainee->activeGroup->group->group_code);
                $trainee->agency()->associate($agency_id);

                $trainee->save();
        });
    }
}

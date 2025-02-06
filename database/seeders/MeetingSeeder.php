<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Meetings\Meeting;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('meeting_types')->insert([
            ['name' => '1:1 meeting', 'description' => 'A personal 1:1 meeting with an instructor.'],
            ['name' => '2:1 meeting', 'description' => 'A two-on-one meeting with a trainee and an instructor.'],
            ['name' => 'group meeting', 'description' => 'A group meeting with several trainees and an instructor.'],
            ['name' => 'support session', 'description' => 'A support session for trainees struggling with their studies.'],
        ]);

        DB::table('meeting_statuses')->insert([
            ['name' => 'Completed', 'description' => 'You met the goals of the unit.'],
            ['name' => 'Incomplete', 'description' => 'You did not meet the goals of the unit and have to re-do this meeting or the unit.'],
            ['name' => 'No-show', 'description' => 'You did not show up at the scheduled meeting.'],
        ]);

        $assignments = [];
        $soloMeetings = [];
        $groupMeetings = [];

        // Assignments for each unit
        for ($unitId = 1; $unitId <= 28; $unitId++) {
            $assignments[] = [
                'unit_id' => $unitId,
                'name' => "Assignment for Unit $unitId",
                'internal_name' => "Internal name for the assignment for Unit $unitId",
                'description' => "Description for the assignment of unit $unitId.",
                'internal_notes' => "Notes that are only visible for the instructors in the assignment of unit $unitId.",
                'submission_type' => 'text',
                'attachment_type' => null,
                'slug' => Str::slug("assignment-unit-$unitId"),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Solo meetings for all units
            $soloMeetings[] = [
                'unit_id' => $unitId,
                'meeting_type_id' => 1,
                'description' => '1:1 about unit ' . $unitId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Group meetings for units 1–8
            if ($unitId <= 8) {
                $groupMeetings[] = [
                    'unit_id' => $unitId,
                    'meeting_type_id' => 3,
                    'description' => 'group meeting about unit ' . $unitId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('assignments')->insert($assignments);
        DB::table('meetings')->insert($soloMeetings);
        DB::table('meetings')->insert($groupMeetings);
    }
}

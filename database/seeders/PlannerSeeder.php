<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Planner\PlannerWeek;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlannerSeeder extends Seeder
{
    public function run()
    {
        // Add 7 planned weeks to the planner_weeks table
        $currentDate = Carbon::now();

        for ($i = -3; $i <= 4; $i++) {
            $weekStartDate = $currentDate->copy()->addWeeks($i)->startOfWeek(Carbon::MONDAY);
            $weekEndDate = $currentDate->copy()->addWeeks($i)->endOfWeek(Carbon::SUNDAY);

            PlannerWeek::factory()->create([
                'number' => $weekStartDate->isoWeek(),
                'year' => $weekStartDate->year,
                'start_date' => $weekStartDate,
                'end_date' => $weekEndDate,
            ]);
         }

        // The same schedule will be used in production initially
        DB::table('planner_curricula')->insert([
            ['name' => 'Nurses\' curriculum A1.1 - group meetings'],
            ['name' => 'Nurses\' curriculum A2.1 - fast'],
            ['name' => 'Nurses\' curriculum A2.1 - slow'],
            ['name' => 'Chefs\' curriculum'],
        ]);

        // Assign curricula/um to groups
        $curriculumId = 1;
        $groupIds = range(2, 25);

        foreach ($groupIds as $groupId) {
            DB::table('planner_group_curricula')->insert([
                'group_id' => $groupId,
                'planner_curriculum_id' => $curriculumId,
                'is_active' => true,
                'sort' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $curriculumContents = [
            [
                // Unit 1, no meetings, id 1
                'planner_curriculum_id' => 1,
                'content_type' => 'meeting_only',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 1,
            ],
            [
                // Unit 2, meeting 29, id 2
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 2,
            ],
            [
                // Unit 3, meeting 30, id 3
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 3,
            ],
            [
                // Unit 4, meeting 31, id 4
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 4,
            ],
            [
                // Unit 5, meeting 32, id 5
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 5,
            ],
            [
                // Unit 1–5 test, id 6
                'planner_curriculum_id' => 1,
                'content_type' => 'custom_content',
                'custom_content' => 'Unit 1–5 test',
                'show_custom_content' => true,
                'sort' => 6,
            ],
            [
                // Unit 6, meeting 33, id 7
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 7,
            ],
            [
                // Unit 7, meeting 34, id 8
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 8,
            ],
            [
                // Unit 8, meeting 35, id 9
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 9,
            ],
            [
                // Unit 9, meeting 36, id 10
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 10,
            ],
            [
                // Unit 10, meeting 9, id 11
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 11,
            ],
            [
                // Unit 11, meeting 10, id 12
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 12,
            ],
            [
                // Unit 12, meeting 11, id 13
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 13,
            ],
            [
                // Unit 13, meeting 12, id 14
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 14,
            ],
            [
                // A1.3 assessment, id 15
                'planner_curriculum_id' => 1,
                'content_type' => 'custom_content',
                'custom_content' => 'A1.3 assessment',
                'show_custom_content' => true,
                'sort' => 15,
            ],
            [
                // Unit 14, meeting 13, id 16
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 16,
            ],
            [
                // Unit 15, meeting 14, id 17
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 17,
            ],
            [
                // Unit 16, meeting 15, id 18
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 18,
            ],
            [
                // Unit 17, meeting 16, id 19
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 19,
            ],
            [
                // Unit 18, meeting 17, id 20
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 20,
            ],
            [
                // Unit 19, meeting 18, id 21
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 21,
            ],
            [
                // Unit 20, meeting 19, id 22
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 22,
            ],
            [
                // A2.1 assessment, id 23
                'planner_curriculum_id' => 1,
                'content_type' => 'custom_content',
                'custom_content' => 'A2.1 assessment',
                'show_custom_content' => true,
                'sort' => 23,
            ],
            [
                // Unit 21, meeting 20, id 24
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 24,
            ],
            [
                // Unit 22, meeting 21, id 25
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 25,
            ],
            [
                // Unit 23, meeting 22, id 26
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 26,
            ],
            [
                // A2.2 assessment, id 27
                'planner_curriculum_id' => 1,
                'content_type' => 'custom_content',
                'custom_content' => 'A2.2 assessment',
                'show_custom_content' => true,
                'sort' => 27,
            ],
            [
                // Unit 24, meeting 23, id 28
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 28,
            ],
            [
                // Unit 25, meeting 24, id 29
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 29,
            ],
            [
                // Unit 26, meeting 25, id 30
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 30,
            ],
            [
                // Unit 27, meeting 26, id 31
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 31,
            ],
            [
                // Unit 28, meeting 27, id 32
                'planner_curriculum_id' => 1,
                'content_type' => 'default',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 32,
            ],
            [
                // Nothing after unit 28, meeting 28, id 33
                'planner_curriculum_id' => 1,
                'content_type' => 'meeting_only',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 33,
            ],
            [
                // id 34
                'planner_curriculum_id' => 1,
                'content_type' => 'none',
                'custom_content' => null,
                'show_custom_content' => false,
                'sort' => 34,
            ]

        ];

        foreach ($curriculumContents as $content) {
            $contentId = DB::table('planner_curriculum_contents')->insertGetId($content);

            // Insert into pivot tables
            DB::table('planner_curriculum_content_unit')->insert([
                [
                    // Unit 1, no meetings
                    'planner_curriculum_content_id' => 1,
                    'unit_id' => 1, 
                ],
                [
                    // Unit 2, meeting 29
                    'planner_curriculum_content_id' => 2,
                    'unit_id' => 2, 
                ],
                [
                    // Unit 3, meeting 30
                    'planner_curriculum_content_id' => 3,
                    'unit_id' => 3, 
                ],
                [
                    // Unit 4, meeting 31
                    'planner_curriculum_content_id' => 4,
                    'unit_id' => 4, 
                ],
                [
                    // Unit 5, meeting 32
                    'planner_curriculum_content_id' => 5,
                    'unit_id' => 5, 
                ],
                [
                    // Post unit 1–5 test
                    'planner_curriculum_content_id' => 7,
                    'unit_id' => 6, 
                ],
                [
                    // Unit 6, meeting 33
                    'planner_curriculum_content_id' => 8,
                    'unit_id' => 7,
                ],
                [
                    // Unit 7, meeting 34
                    'planner_curriculum_content_id' => 9,
                    'unit_id' => 8,
                ],
                [
                    // Unit 8, meeting 35
                    'planner_curriculum_content_id' => 10,
                    'unit_id' => 9,
                ],
                [
                    // Unit 9, meeting 36
                    'planner_curriculum_content_id' => 11,
                    'unit_id' => 10,
                ],
                [
                    // Unit 10, meeting 9
                    'planner_curriculum_content_id' => 12,
                    'unit_id' => 11,
                ],
                [
                    // Unit 11, meeting 10
                    'planner_curriculum_content_id' => 13,
                    'unit_id' => 12,
                ],
                [
                    // Unit 12, meeting 11
                    'planner_curriculum_content_id' => 14,
                    'unit_id' => 13,
                ],
                [
                    // Unit 13, meeting 12
                    'planner_curriculum_content_id' => 16,
                    'unit_id' => 14,
                ],
                [
                    // Post A1.3 assessment, unit 14, meeting 13
                    'planner_curriculum_content_id' => 17,
                    'unit_id' => 15,
                ],
                [
                    // Unit 15, meeting 14
                    'planner_curriculum_content_id' => 18,
                    'unit_id' => 16,
                ],
                [
                    // Unit 16, meeting 15
                    'planner_curriculum_content_id' => 19,
                    'unit_id' => 17,
                ],
                [
                    // Unit 17, meeting 16
                    'planner_curriculum_content_id' => 20,
                    'unit_id' => 18,
                ],
                [
                    // Unit 18, meeting 17
                    'planner_curriculum_content_id' => 21,
                    'unit_id' => 19,
                ],
                [
                    // Unit 19, meeting 18
                    'planner_curriculum_content_id' => 22,
                    'unit_id' => 20,
                ],
                [
                    // Unit 20, meeting 19
                    'planner_curriculum_content_id' => 24,
                    'unit_id' => 21,
                ],
                [
                    // Post A2.1 assessment, unit 21, meeting 20
                    'planner_curriculum_content_id' => 25,
                    'unit_id' => 22,
                ],
                [
                    // Unit 22, meeting 21
                    'planner_curriculum_content_id' => 26,
                    'unit_id' => 23,
                ],
                [
                    // Unit 23, meeting 22
                    'planner_curriculum_content_id' => 27,
                    'unit_id' => 24,
                ],
                [
                    // Post A2.2 assessment, unit 24, meeting 23
                    'planner_curriculum_content_id' => 29,
                    'unit_id' => 25,
                ],
                [
                    // Unit 25, meeting 24
                    'planner_curriculum_content_id' => 30,
                    'unit_id' => 26,
                ],
                [
                    // Unit 26, meeting 25
                    'planner_curriculum_content_id' => 31,
                    'unit_id' => 27,
                ],
                [
                    // Unit 27, meeting 26
                    'planner_curriculum_content_id' => 32,
                    'unit_id' => 28,
                ],
                [
                    // Unit 28, meeting 27
                    'planner_curriculum_content_id' => 33,
                    'unit_id' => 29,
                ]

            ]);

            DB::table('planner_curriculum_content_meeting')->insert([
                [
                    // Unit 2, meeting 29
                    'planner_curriculum_content_id' => 2,
                    'meeting_id' => 29, 
                ],
                [
                    'planner_curriculum_content_id' => 3,
                    'meeting_id' => 30, 
                ],
                [
                    'planner_curriculum_content_id' => 4,
                    'meeting_id' => 31, 
                ],
                [
                    'planner_curriculum_content_id' => 5,
                    'meeting_id' => 32, 
                ],
                [
                    'planner_curriculum_content_id' => 7,
                    'meeting_id' => 33, 
                ],
                [
                    'planner_curriculum_content_id' => 8,
                    'meeting_id' => 34, 
                ],
                [
                    'planner_curriculum_content_id' => 9,
                    'meeting_id' => 35,
                ],
                [
                    'planner_curriculum_content_id' => 10,
                    'meeting_id' => 36,
                ],
                [
                    'planner_curriculum_content_id' => 11,
                    'meeting_id' => 9,
                ],
                [
                    'planner_curriculum_content_id' => 12,
                    'meeting_id' => 10,
                ],
                [
                    'planner_curriculum_content_id' => 13,
                    'meeting_id' => 11,
                ],
                [
                    'planner_curriculum_content_id' => 15,
                    'meeting_id' => 12,
                ],
                [
                    'planner_curriculum_content_id' => 16,
                    'meeting_id' => 13,
                ],
                [
                    'planner_curriculum_content_id' => 17,
                    'meeting_id' => 14,
                ],
                [
                    'planner_curriculum_content_id' => 18,
                    'meeting_id' => 15,
                ],
                [
                    'planner_curriculum_content_id' => 19,
                    'meeting_id' => 16,
                ],
                [
                    'planner_curriculum_content_id' => 20,
                    'meeting_id' => 17,
                ],
                [
                    'planner_curriculum_content_id' => 21,
                    'meeting_id' => 18,
                ],
                [
                    'planner_curriculum_content_id' => 22,
                    'meeting_id' => 19,
                ],
                [
                    'planner_curriculum_content_id' => 23,
                    'meeting_id' => 20,
                ],
                [
                    'planner_curriculum_content_id' => 25,
                    'meeting_id' => 21,
                ],
                [
                    'planner_curriculum_content_id' => 26,
                    'meeting_id' => 22,
                ],
                [
                    'planner_curriculum_content_id' => 27,
                    'meeting_id' => 23,
                ],
                [
                    'planner_curriculum_content_id' => 29,
                    'meeting_id' => 24,
                ],
                [
                    'planner_curriculum_content_id' => 30,
                    'meeting_id' => 25,
                ],
                [
                    'planner_curriculum_content_id' => 31,
                    'meeting_id' => 26,
                ],
                [
                    'planner_curriculum_content_id' => 32,
                    'meeting_id' => 27,
                ],
                [
                    'planner_curriculum_content_id' => 33,
                    'meeting_id' => 28,
                ]

            ]);
        }

        // Populate planner_weekly_schedules for each group
        $weekIds = range(1, 7);
        $sortNumbers = range(1, 20);

        foreach ($groupIds as $groupId) {
            $currentSortIndex = array_rand($sortNumbers);

            foreach ($weekIds as $weekId) {
                $sortNumber = $sortNumbers[$currentSortIndex % count($sortNumbers)];
                $curriculumContent = DB::table('planner_curriculum_contents')
                    ->where('planner_curriculum_id', $curriculumId)
                    ->where('sort', $sortNumber)
                    ->first();
        
                if ($curriculumContent) {
                    $units = DB::table('planner_curriculum_content_unit')
                        ->where('planner_curriculum_content_id', $curriculumContent->id)
                        ->pluck('unit_id')
                        ->unique()
                        ->toArray();
        
                    $meetings = DB::table('planner_curriculum_content_meeting')
                        ->where('planner_curriculum_content_id', $curriculumContent->id)
                        ->pluck('meeting_id')
                        ->unique()
                        ->toArray();

                    DB::table('planner_weekly_schedules')->insert([
                        'group_id' => $groupId,
                        'planner_week_id' => $weekId,
                        'planner_curriculum_contents_id' => $curriculumContent->id,
                        'units' => json_encode($units),
                        'meetings' => json_encode($meetings),
                        'content_type' => $curriculumContent->content_type,
                        'custom_content' => $curriculumContent->custom_content,
                        'show_custom_content' => $curriculumContent->show_custom_content,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
        
                $currentSortIndex++;
            }
        }
    
    }
}
<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds. Comment out the updates at the end before running the seeder initially in production.
     */
    public function run(): void
    {
        $futureDate1 = Carbon::now()->addWeeks(1);
        $futureDate2 = Carbon::now()->addWeeks(2);
        $thisWeekDate = Carbon::now()->startOfWeek();
        $pastDate = Carbon::now()->subMonths(2);

        DB::table('proficiencies')->insert([
            ['name' => 'Unit 1–5 mastery', 'description' => 'The trainee has the knowledge of units 1–5.', ],
            ['name' => 'A1.3 level', 'description' => 'The trainee has reached the A1.3 proficiency in Finnish.', ],
            ['name' => 'A2.1 level', 'description' => 'The trainee has reached the A2.1 proficiency in Finnish.', ],
            ['name' => 'A2.2 level', 'description' => 'The trainee has reached the A2.2 proficiency in Finnish.', ],
            ['name' => 'B1.1 level', 'description' => 'The trainee has reached the B1.1 proficiency in Finnish.', ],
        ]);

        DB::table('exams')->insert([
            ['name' => 'Unit 1–5 test', 'type' => 'test', 'proficiency_id' => 1, 'date' => null, 'any_instructor_can_grade' => true],
            ['name' => 'A1.3 assessment', 'type' => 'assessment', 'proficiency_id' => 2, 'date' => null, 'any_instructor_can_grade' => true ],
            ['name' => 'A2.1 assessment', 'type' => 'assessment', 'proficiency_id' => 3, 'date' => null, 'any_instructor_can_grade' => true ],
            ['name' => 'A2.2 assessment', 'type' => 'assessment', 'proficiency_id' => 4, 'date' => null, 'any_instructor_can_grade' => true ],
            ['name' => 'A1.3 exam (template)', 'type' => 'exam', 'proficiency_id' => 2, 'date' => null, 'any_instructor_can_grade' => false ],
            ['name' => 'A2.1 exam (template)', 'type' => 'exam', 'proficiency_id' => 3, 'date' => null, 'any_instructor_can_grade' => false ],
            ['name' => 'A2.2 exam (template)', 'type' => 'exam', 'proficiency_id' => 4, 'date' => null, 'any_instructor_can_grade' => false ],
            ['name' => 'A2.2 exam, FIN120', 'type' => 'exam', 'proficiency_id' => 4, 'date' => $futureDate1, 'any_instructor_can_grade' => false ],
            ['name' => 'A2.1 exam, INTW203', 'type' => 'exam', 'proficiency_id' => 3, 'date' => $futureDate2, 'any_instructor_can_grade' => false ],
            ['name' => 'A2.1 exam FIN202, FIN205', 'type' => 'exam', 'proficiency_id' => 3, 'date' => $thisWeekDate, 'any_instructor_can_grade' => false ],
            ['name' => 'A2.1 exam FIN9', 'type' => 'exam', 'proficiency_id' => 3, 'date' => $pastDate, 'any_instructor_can_grade' => false ],
        ]);

        DB::table('exam_tasks')->insert([
            // Unit 1–5 test
            ['name' => 'Unit 1–5 test', 'short_name' => 'Unit 1–5 test', 'description' => 'All unit 1–5 test tasks', 'max_score' => 24, 'passing_score' => null, 'mandatory_to_pass' => false ],

            // A2.1 assessment
            ['name' => 'A2.1 picture 1', 'short_name' => 'picture 1', 'description' => 'Random picture #1', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 picture 2', 'short_name' => 'picture ', 'description' => 'Random picture #2', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 question 1', 'short_name' => 'question 1', 'description' => 'Random question #1', 'max_score' => 3, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 question 2', 'short_name' => 'question 2', 'description' => 'Random question #2', 'max_score' => 3, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 question 3', 'short_name' => 'question 3', 'description' => 'Random question #3', 'max_score' => 3, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 roleplay + summary','short_name' => 'roleplay + summary',  'description' => 'Random question #2', 'max_score' => 1, 'passing_score' => 1, 'mandatory_to_pass' => true ],
            ['name' => 'A2.1 text types', 'short_name' => 'text types', 'description' => 'Random question #3', 'max_score' => 1, 'passing_score' => 1, 'mandatory_to_pass' => true ],

            //A2.2 assessment
            ['name' => 'A2.2 picture 1', 'short_name' => 'picture 1', 'description' => 'Random picture #1', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 picture 2', 'short_name' => 'picture 2', 'description' => 'Random picture #2', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false],
            ['name' => 'A2.2 picture 3', 'short_name' => 'picture 3', 'description' => 'Random picture #3', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 scenario 1', 'short_name' => 'scenario 1', 'description' => 'Random scenario #1', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 scenario 2', 'short_name' => 'scenario 2', 'description' => 'Random scenario #2', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 scenario 3', 'short_name' => 'scenario 2', 'description' => 'Random scenario #3', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 oral report, past tense', 'short_name' => 'past tense report', 'description' => 'Random question #2', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 oral report, vocabulary', 'short_name' => 'report, vocabulary', 'description' => 'Random question #3', 'max_score' => 2, 'passing_score' => null, 'mandatory_to_pass' => false ],

            // A2.1 exam
            ['name' => 'A2.1 exam listening A, B, C', 'short_name' => 'listening A, B, C', 'description' => 'Listening A, B, C', 'max_score' => 10, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 exam listening D', 'short_name' => 'listening D', 'description' => 'Listening D', 'max_score' => 5, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 exam listening E', 'short_name' => 'listening E', 'description' => 'Listening E', 'max_score' => 10, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 exam vocabulary', 'short_name' => 'vocabulary', 'description' => 'Vocabulary', 'max_score' => 15, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 exam writing', 'short_name' => 'writing', 'description' => 'Writing', 'max_score' => 15, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 exam reading', 'short_name' => 'reading', 'description' => 'Reading', 'max_score' => 15, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 exam speaking', 'short_name' => 'speaking', 'description' => 'Oral task with the instructor', 'max_score' => 12, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.1 exam recording', 'short_name' => 'recording', 'description' => 'Oral recording task', 'max_score' => 6, 'passing_score' => null, 'mandatory_to_pass' => false ],

            // A2.2 exam
            ['name' => 'A2.2 exam listening A, B, C', 'short_name' => 'listening A, B, C', 'description' => 'Listening A, B, C', 'max_score' => 10, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 exam listening D', 'short_name' => 'listening D', 'description' => 'Listening D', 'max_score' => 5, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 exam listening E', 'short_name' => 'listening E', 'description' => 'Listening E', 'max_score' => 10, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 exam vocabulary', 'short_name' => 'vocabulary', 'description' => 'Vocabulary', 'max_score' => 15, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 exam writing', 'short_name' => 'writing', 'description' => 'Writing', 'max_score' => 15, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 exam reading', 'short_name' => 'reading', 'description' => 'Reading', 'max_score' => 15, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 exam speaking', 'short_name' => 'speaking', 'description' => 'Oral task with the instructor', 'max_score' => 12, 'passing_score' => null, 'mandatory_to_pass' => false ],
            ['name' => 'A2.2 exam recording', 'short_name' => 'recording', 'description' => 'Oral recording task', 'max_score' => 6, 'passing_score' => null, 'mandatory_to_pass' => false ],
        ]);

        DB::table('exam_sections')->insert([
            ['name' => 'Unit 1–5 test', 'short_name' => 'Unit 1–5 test','passing_percentage' => 70 ],
            ['name' => 'A1.3 assessment', 'short_name' => 'A1.3 assessment', 'passing_percentage' => 70 ],
            ['name' => 'A2.1 assessment', 'short_name' => 'A2.1 assessment', 'passing_percentage' => 70 ],
            ['name' => 'A2.2 assessment', 'short_name' => 'A2.2 assessment', 'passing_percentage' => 70 ],
            ['name' => 'A2.1 exam, writing', 'short_name' => 'writing', 'passing_percentage' => 70 ],
            ['name' => 'A2.1 exam, oral', 'short_name' => 'oral', 'passing_percentage' => 70 ],
            ['name' => 'A2.2 exam, writing', 'short_name' => 'writing', 'passing_percentage' => 70 ],
            ['name' => 'A2.2 exam, oral', 'short_name' => 'oral', 'passing_percentage' => 70 ],
        ]);

        DB::table('exam_section_task')->insert([
            ['exam_task_id' => 1, 'exam_section_id' => 1 ],
            ['exam_task_id' => 2, 'exam_section_id' => 3 ],
            ['exam_task_id' => 3, 'exam_section_id' => 3 ],
            ['exam_task_id' => 4, 'exam_section_id' => 3 ],
            ['exam_task_id' => 5, 'exam_section_id' => 3 ],
            ['exam_task_id' => 6, 'exam_section_id' => 3 ],
            ['exam_task_id' => 7, 'exam_section_id' => 3 ],
            ['exam_task_id' => 8, 'exam_section_id' => 3 ],
            ['exam_task_id' => 9, 'exam_section_id' => 4 ],
            ['exam_task_id' => 10, 'exam_section_id' => 4 ],
            ['exam_task_id' => 11, 'exam_section_id' => 4 ],
            ['exam_task_id' => 12, 'exam_section_id' => 4 ],
            ['exam_task_id' => 13, 'exam_section_id' => 4 ],
            ['exam_task_id' => 14, 'exam_section_id' => 4 ],
            ['exam_task_id' => 15, 'exam_section_id' => 4 ],
            ['exam_task_id' => 16, 'exam_section_id' => 4 ],
            ['exam_task_id' => 17, 'exam_section_id' => 5 ],
            ['exam_task_id' => 18, 'exam_section_id' => 5 ],
            ['exam_task_id' => 19, 'exam_section_id' => 5 ],
            ['exam_task_id' => 20, 'exam_section_id' => 5 ],
            ['exam_task_id' => 21, 'exam_section_id' => 5 ],
            ['exam_task_id' => 22, 'exam_section_id' => 5 ],
            ['exam_task_id' => 23, 'exam_section_id' => 6 ],
            ['exam_task_id' => 24, 'exam_section_id' => 6 ],
            ['exam_task_id' => 25, 'exam_section_id' => 7 ],
            ['exam_task_id' => 26, 'exam_section_id' => 7 ],
            ['exam_task_id' => 27, 'exam_section_id' => 7 ],
            ['exam_task_id' => 28, 'exam_section_id' => 7 ],
            ['exam_task_id' => 29, 'exam_section_id' => 7 ],
            ['exam_task_id' => 30, 'exam_section_id' => 7 ],
        ]);

        DB::table('exam_exam_section')->insert([
            // Unit 1–5 test
            ['exam_id' => 1, 'exam_section_id' => 1 ],

            // Assessments
            ['exam_id' => 3, 'exam_section_id' => 3 ],
            ['exam_id' => 4, 'exam_section_id' => 4 ],

            // A2.1 exams
            ['exam_id' => 6, 'exam_section_id' => 5 ],
            ['exam_id' => 6, 'exam_section_id' => 6 ],

            ['exam_id' => 9, 'exam_section_id' => 5 ],
            ['exam_id' => 9, 'exam_section_id' => 6 ],

            ['exam_id' => 10, 'exam_section_id' => 5 ],
            ['exam_id' => 10, 'exam_section_id' => 6 ],

            ['exam_id' => 11, 'exam_section_id' => 5 ],
            ['exam_id' => 11, 'exam_section_id' => 6 ],

            // A2.2 assessments
            ['exam_id' => 7, 'exam_section_id' => 7 ],
            ['exam_id' => 7, 'exam_section_id' => 8 ],

            ['exam_id' => 8, 'exam_section_id' => 7 ],
            ['exam_id' => 8, 'exam_section_id' => 8 ],
        ]);

        // These should be commented out/removed before the initial seeding in production
        DB::table('exams')
            ->where('name', 'A2.2 exam, FIN120')
            ->update(['allowed_instructors' => json_encode([6, 7, 8])]);

        DB::table('exams')
            ->where('name', 'A2.1 exam, INTW203')
            ->update(['allowed_instructors' => json_encode([6, 7])]);

        DB::table('exams')
            ->where('name', 'A2.1 exam FIN202, FIN205')
            ->update(['allowed_instructors' => json_encode([7, 8])]);

        DB::table('exams')
            ->where('name', 'A2.1 exam FIN9')
            ->update(['allowed_instructors' => json_encode([6, 8])]);

    }
}

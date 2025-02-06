<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Initial production seeding
        // $this->call([
        //     CountrySeeder::class,
        //     RoleSeeder::class,
        //     FlagSeeder::class,
        //     GroupTypeSeeder::class,
        //     CourseSeeder::class,
        //     ExamSeeder::class,
        //     GroupSeeder::class,
        //     UserSeeder::class,
        // ]);

        // Development deployment
        $this->call([
            CountrySeeder::class,
            AgencySeeder::class,
            RoleSeeder::class,
            KnowledgeBaseSeeder::class,
            FlagSeeder::class,
            GroupTypeSeeder::class,
            CourseSeeder::class,
            MeetingSeeder::class,
            ExamSeeder::class,
            GroupSeeder::class,
            PlannerSeeder::class,
            DocumentSeeder::class,
            UserSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}

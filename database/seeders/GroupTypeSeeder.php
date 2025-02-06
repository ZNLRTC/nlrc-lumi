<?php

namespace Database\Seeders;

use App\Models\Grouping\GroupType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    use WithoutModelEvents;
     
    public function run(): void
    {
        GroupType::insert([
            ['code' => 'SUO', 'description' => 'Direct recruitment'],
            ['code' => 'SUOM', 'description' => 'Direct recruiment, mass batches.'],
            ['code' => 'FIN', 'description' => 'Groups under Topmake.'],
            ['code' => 'KAI', 'description' => 'Direct recruiment, Kainuu groups.'],
            ['code' => 'KEN', 'description' => 'Africa Work Power groups.'],
            ['code' => 'INTW', 'description' => 'Walsons groups, India-based.'],
            ['code' => 'INTD', 'description' => 'NSDCI groups, India-based.'],
            ['code' => 'KOKKI', 'description' => 'Direct recruitment, chef groups.'],
            ['code' => 'KMH', 'description' => 'Trainees doing the Kyl mä hoidan beginners\' course.']
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Grouping\Group;
use Illuminate\Database\Seeder;
use App\Models\Grouping\GroupType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    use WithoutModelEvents;

    public function run(): void
    {
        // Beginners course
        Group::insert([
            ['group_type_id' => 9, 'name' => 'Kyl mä hoidan', 'date_of_start' => '2024-11-18', 'active' => 1],
        ]);

        Group::factory()->count(24)->create()->each(function ($group) {
            $group->courses()->attach(2);
        });
    }
}

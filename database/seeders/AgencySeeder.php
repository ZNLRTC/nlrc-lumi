<?php

namespace Database\Seeders;

use App\Models\Agencies\Agency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    use WithoutModelEvents;

    public function run(): void
    {
        Agency::insert([
            ['name' => 'Silk Road', 'active' => 1],
            ['name' => 'Topmake International Manpower Services', 'active' => 1],
            ['name' => 'Africa Workpower', 'active' => 1],
            ['name' => 'NSDCI', 'active' => 1],
            ['name' => 'Walsons Healthcare', 'active' => 1],
            ['name' => 'Uncategorized Agency', 'active' => 1]
        ]);
    }
}

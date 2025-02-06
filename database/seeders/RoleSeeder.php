<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    use WithoutModelEvents;

    public function run(): void
    {
        Role::insert([
            ['name' => 'Admin', 'description' => 'System administrator.'],
            ['name' => 'Staff', 'description' => 'General user role for staff.'],
            ['name' => 'Observer', 'description' => 'External observer with limited editing access.'],
            ['name' => 'Trainee', 'description' => 'All trainees.'],
            ['name' => 'Instructor', 'description' => 'Language instructors.'],
            ['name' => 'Editing instructor', 'description' => 'Language instructors who are allowed to modify course content.'],
            ['name' => 'Manager', 'description' => 'Office and training managers who have broader deletion/edit options than staff.'],
        ]);
    }
}

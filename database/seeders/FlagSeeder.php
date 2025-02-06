<?php

namespace Database\Seeders;

use App\Models\Flag\Flag;
use App\Models\Flag\FlagType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    use WithoutModelEvents;
    
    public function run(): void
    {
        FlagType::insert([
            ['name' => 'Global', 'notes' => 'Flags used by all teams.'],
            ['name' => 'Training', 'notes' => 'Flags used by training team.'],
            ['name' => 'Recruitment', 'notes' => 'Flags used by recruitment.'],
            ['name' => 'Processing', 'notes' => 'Flags used by processing.'],
            ['name' => 'Integration', 'notes' => 'Flags by deployment folks.'],
            ['name' => 'Other', 'notes' => 'Unspecified Flags.'],
        ]);

        Flag::insert([
            [
                'flag_type_id' => 1,
                'name' => 'Deployed',
                'description' => 'The trainee has been deployed to Finland.',
                'visible_to_trainee' => 1,
                'active' => 1
            ],
            [
                'flag_type_id' => 1,
                'name' => 'Reported',
                'description' => 'The trainee has been reported for some reason.',
                'visible_to_trainee' => 0,
                'active' => 1
            ],
            [
                'flag_type_id' => 1,
                'name' => 'Quit',
                'description' => 'The trainee quit the training entirely.',
                'visible_to_trainee' => 1,
                'active' => 1
            ],
            [
                'flag_type_id' => 1,
                'name' => 'Active',
                'description' => 'The trainee is currently having the training.',
                'visible_to_trainee' => 1,
                'active' => 1
            ],
            [
                'flag_type_id' => 1,
                'name' => 'Inactive',
                'description' => 'The trainee cannot be reached or communication is impossible, among other reasons.',
                'visible_to_trainee' => 1,
                'active' => 1
            ],
            [
                'flag_type_id' => 2,
                'name' => 'Waiting',
                'description' => 'The trainee is waiting for a test or a group assignment.',
                'visible_to_trainee' => 1,
                'active' => 1
            ],
            [
                'flag_type_id' => 2,
                'name' => 'On hold',
                'description' => 'The trainee has incomplete documents, among other reasons.',
                'visible_to_trainee' => 1,
                'active' => 1
            ],
            [
                'flag_type_id' => 6,
                'name' => 'Other',
                'description' => 'Specify a custom flag.',
                'visible_to_trainee' => 1,
                'active' => 1
            ]
        ]);
    }
}

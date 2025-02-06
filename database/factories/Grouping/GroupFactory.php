<?php

namespace Database\Factories\Grouping;

use App\Models\Agencies\Agency;
use App\Models\Grouping\GroupType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class GroupFactory extends Factory
{
    public function definition(): array
    {
        // $group_code = $this->faker->randomElement(['SUO', 'SUOM', 'FIN', 'KAI', 'KEN', 'INTW', 'INTD', 'KOKKI']);
        // $group_type = GroupType::where('code', $group_code)->first();
        // $agency_id = Agency::get_agency_id_by_group_name($group_code);

        // This makes random group codes, but can be used in tests without it running out of uniques that easily
        $group_type = GroupType::factory()->create();
        $agency_id = Agency::get_agency_id_by_group_name($group_type->code);

        return [
            'group_type_id' => $group_type->id,
            'agency_id' => $agency_id,
            'name' => $this->faker->unique()->numberBetween(100, 999),
            'date_of_start' => $this->faker->dateTimeBetween('-1 years', '-1 month')->format('Y-m-d'),
            'active' => 1,
        ];
    }
}

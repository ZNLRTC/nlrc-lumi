<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => Role::firstOrCreate(['name' => 'Trainee'], ['description' => 'A trainee!']),
            'name' => Str::random(10),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'profile_photo_path' => null,
            'timezone' => fake()->timezone(),
            'notification_settings' => ['meetings_on_call' => 1], // Currently applies to trainees only
            'password' => static::$password ??= Hash::make('password'),
            'restricted' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function observers(): static
    {
        $observer_role_id = Role::where('name', 'Observer')
            ->first()
            ->id;
        $sample_timezones = ['Asia/Colombo', 'Asia/Manila', 'Europe/Helsinki'];

        return $this->state(new Sequence(
            fn (Sequence $sequence) => [
                'role_id' => $observer_role_id,
                'name' => 'Observer ' .($sequence->index + 1),
                'email' => 'observer@silkroad.fi',
                'timezone' => fake()->randomElement($sample_timezones)
            ],
            fn (Sequence $sequence) => [
                'role_id' => $observer_role_id,
                'name' => 'Observer ' .($sequence->index + 1),
                'email' => 'observer@topmake.ph',
                'timezone' => fake()->randomElement($sample_timezones)
            ],
            fn (Sequence $sequence) => [
                'role_id' => $observer_role_id,
                'name' => 'Observer ' .($sequence->index + 1),
                'email' => 'observer@africaworkpower.com',
                'timezone' => fake()->randomElement($sample_timezones)
            ],
            fn (Sequence $sequence) => [
                'role_id' => $observer_role_id,
                'name' => 'Observer ' .($sequence->index + 1),
                'email' => 'observer@nsdcinternational.com',
                'timezone' => fake()->randomElement($sample_timezones)
            ],
            fn (Sequence $sequence) => [
                'role_id' => $observer_role_id,
                'name' => 'Observer ' .($sequence->index + 1),
                'email' => 'observer@walsonshealthcare.com',
                'timezone' => fake()->randomElement($sample_timezones)
            ]
        ));
    }

    // public function configure()
    // {
    //     return $this->afterCreating(function (User $user) {
    //         $user->trainee()->create();
    //     });
    // }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }

    /**
     * Indicate that the user should have a personal team.
     */
    // public function withPersonalTeam(callable $callback = null): static
    // {
    //     if (! Features::hasTeamFeatures()) {
    //         return $this->state([]);
    //     }

    //     return $this->has(
    //         Team::factory()
    //             ->state(fn (array $attributes, User $user) => [
    //                 'name' => $user->name.'\'s Team',
    //                 'user_id' => $user->id,
    //                 'personal_team' => true,
    //             ])
    //             ->when(is_callable($callback), $callback),
    //         'ownedTeams'
    //     );
    // }
}

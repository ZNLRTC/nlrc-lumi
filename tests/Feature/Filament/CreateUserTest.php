<?php

use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Filament\Admin\Resources\UserResource;
use App\Mail\ManuallyCreatedNewUserPasswordMail;
use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;

function createUserData($overrides = [])
{
    return array_merge([
        'name' => fake()->name,
        'email' => fake()->unique()->safeEmail,
        'password' => 'very saf€ pa$$w0rd',
        'timezone' => fake()->timezone,
        'role_id' => Role::firstOrCreate(['name' => 'Instructor'], ['description' => 'An instructor!'])->id,
        'send_email' => false,
    ], $overrides);
}

describe('Create user', function () {

    test('only admins can access the user creation page/form', function () {
        $admin = createAdmin();
        $staff = createStaff();
        $manager = createManager();
        $trainee = createTrainee();

        $this->actingAs($admin)
            ->get(UserResource::getUrl('create'))
            ->assertSuccessful();

        $this->actingAs($staff)
            ->get(UserResource::getUrl('create'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->get(UserResource::getUrl('create'))
            ->assertForbidden();

        $this->actingAs($trainee)
            ->get(UserResource::getUrl('create'))
            ->assertForbidden();
    });

    it('allows admins to create a new user', function () {
        $admin = createAdmin();
        $userData = createUserData();
        // dd($userData['email']);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm($userData)
            ->call('create')
            ->assertHasNoErrors();

        $createdUser = User::where('email', $userData['email'])->first();

        $this->assertDatabaseHas('users', [
            'name' => $createdUser->name,
            'email' => $createdUser->email,
            'timezone' => $createdUser->timezone,
            'role_id' => $createdUser->role_id,
        ]);
    });

    it('hashes the password of a user created via Filament form', function () {
        $admin = createAdmin();
        $userData = createUserData();

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm($userData)
            ->call('create')
            ->assertHasNoErrors();

        $createdUser = User::where('email', $userData['email'])->first();

        expect(Hash::check($userData['password'], $createdUser->password))->toBeTrue();
    });

    it('emails the new user if prompted', function() {
        $admin = createAdmin();
        $userData = createUserData(['send_email' => true]);

        Mail::fake();

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm($userData)
            ->call('create')
            ->assertHasNoErrors();

        Mail::assertSent(ManuallyCreatedNewUserPasswordMail::class, function ($mail) use ($userData) {
            return $mail->hasTo($userData['email']) && $mail->password === $userData['password'];
        });
    });

    it('does not email the new user if the option is not checked', function() {
        $admin = createAdmin();
        $userData = createUserData(['send_email' => false]);

        Mail::fake();

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm($userData)
            ->call('create')
            ->assertHasNoErrors();

        Mail::assertNothingSent();
    });

})->group('create-user');


<?php

use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;

test('user accounts can be deleted', function () {
    $this->actingAs($user = createTrainee());

    $component = Livewire::test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser');

    expect($user->fresh())->toBeNull();
})->skip(function () {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

test('correct password must be provided before account can be deleted', function () {
    $this->actingAs($user = createInstructor());

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'wrong-password')
        ->call('deleteUser')
        ->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
})->skip(function () {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

<?php

use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;

test('other browser sessions can be logged out', function () {
    $this->actingAs(createTrainee());

    Livewire::test(LogoutOtherBrowserSessionsForm::class)
        ->set('password', 'password')
        ->call('logoutOtherBrowserSessions')
        ->assertSuccessful();
});

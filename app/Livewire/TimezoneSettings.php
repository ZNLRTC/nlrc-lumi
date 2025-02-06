<?php

namespace App\Livewire;

use DateTimeZone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class TimezoneSettings extends Component
{
    public $user;
    public $timezone;
    public $timezones;
    public $current_time;

    public function create_timezones_list()
    {
        return DateTimeZone::listIdentifiers(timezoneGroup: DateTimeZone::ALL);
    }

    // Lifecycle hook
    public function updatedTimezone()
    {
        $this->update_current_time();
    }

    public function update_current_time()
    {
        $this->current_time = $this->timezone ? 'It should now be ' .Carbon::now($this->timezone)->format('H:i A'). ' in your area. Select a different timezone if that is not the case.' : '';
    }

    public function update_timezone_settings()
    {
        $this->validate(['timezone' => ['required', Rule::in($this->create_timezones_list())]]);

        $this->user->update(['timezone' => $this->timezone]);

        $this->dispatch('timezone-settings-updated');
    }

    public function mount()
    {
        $this->user = Auth::user();
        $this->timezones = $this->create_timezones_list();
        $this->timezone = $this->user->timezone;

        $this->update_current_time();
    }

    #[On('timezone-settings-updated')]
    public function render()
    {
        return view('livewire.timezone-settings');
    }
}

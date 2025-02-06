<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationSettings extends Component
{
    public $user;
    public $setting_on_call_meetings;

    public function update_notification_settings()
    {
        Auth::user()->update([
            'notification_settings' => ['meetings_on_call' => $this->setting_on_call_meetings == true ? 1 : 0]
        ]);
        Auth::user()->save();

        $this->dispatch('notification-settings-updated');
    }

    public function mount()
    {
        $this->user = Auth::user();
        $this->setting_on_call_meetings = Auth::user()->notification_settings['meetings_on_call'];
    }

    #[On('notification-settings-updated')]
    public function render()
    {
        return view('livewire.notification-settings');
    }
}

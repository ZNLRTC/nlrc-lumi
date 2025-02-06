<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationList extends Component
{
    public $trainee_notifications = [];

    public function set_is_read($notification_id)
    {
        Auth::user()->trainee->unreadNotifications->where('id', $notification_id)->markAsRead();
    }

    public function render()
    {
        $trainee = Auth::user()->trainee;
        if ($trainee) {
            $this->trainee_notifications = $trainee->notifications
                ->slice(0, 15);

            return view('livewire.notification-list');
        }
    }
}

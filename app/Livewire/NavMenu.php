<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

// Using NavigationMenu is conflicting with another class of the same name from Laravel Jetstream
class NavMenu extends Component
{
    public $trainee_notifications = [];
    public $trainee_notifications_unread_count = 0;
    public $trainee_notifications_unread_count_is_overlap = false;
    public $userId;

    // Listen to an event
    #[On('echo-private:receive-announcement.{userId},\App\Events\ReceiveAnnouncementEvent')]
    public function onReceiveAnnouncement($event)
    {
        $this->get_notifications();
    }

    public function get_notifications()
    {
        $trainee = Auth::user()->trainee;
        if ($trainee) {
            $this->trainee_notifications_unread_count = $trainee->unreadNotifications
                ->count();

            $this->trainee_notifications = $trainee->notifications
                ->slice(0, 5);

            $notifications_unread_on_screen = $this->trainee_notifications->filter(function ($notif) {
                return $notif->read_at == null;
            });

            if ($this->trainee_notifications_unread_count > $notifications_unread_on_screen->count()) {
                $this->trainee_notifications_unread_count_is_overlap = true;
            }
        }
    }

    public function mount()
    {
        $this->userId = Auth::user()->id;
    }

    #[On('mark-is-read')]
    #[On('trainee-profile-updated')]
    public function render()
    {
        $this->get_notifications();

        return view('livewire.nav-menu');
    }

    public function set_is_read($notification_id)
    {
        Auth::user()->trainee->unreadNotifications->where('id', $notification_id)->markAsRead();

        $this->dispatch('mark-is-read');
    }
}

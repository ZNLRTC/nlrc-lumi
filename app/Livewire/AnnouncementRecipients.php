<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementRecipients extends Component
{
    use WithPagination;

    public $announcement_id = 0;

    public function render()
    {
        $announcement_recipients = Notification::select(['trainees.first_name', 'trainees.middle_name', 'trainees.last_name', 'notifications.notifiable_type', 'notifications.notifiable_id', 'notifications.read_at', 'notifications.created_at'])
            ->join('trainees', 'trainees.id', 'notifications.notifiable_id')
            ->where('type', 'announcement-sent')
            ->where('notifiable_type', 'App\Models\Trainee')
            ->where('data->announcement_id', $this->announcement_id)
            ->orderBy('notifications.created_at', 'DESC');

        $announcement_recipients = $announcement_recipients->paginate(25);

        return view('livewire.announcements.announcement-recipients', compact('announcement_recipients'));
    }
}

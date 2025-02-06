<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LatestAnnouncement extends Component
{
    public $announcement;

    public function mount(): void
    {
        $trainee = Auth::user()->trainee;
        if ($trainee) {
            $base_query = $trainee->notifications()
                ->where('type', 'announcement-sent');

            $latest_announcement_prioritized = $base_query->clone()
                ->where('data->is_priority', true)
                ->first();

            // If there are no prioritized announcements, show the latest announcement
            if (!$latest_announcement_prioritized) {
                $latest_announcement = $base_query->clone()
                    ->first();

                if ($latest_announcement) {
                    $this->announcement = Announcement::find($latest_announcement->data['announcement_id']);
                }
            } else {
                $this->announcement = Announcement::find($latest_announcement_prioritized->data['announcement_id']);
            }
        }
    }

    public function render(): View
    {
        return view('livewire.announcements.latest-announcement');
    }
}

<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Livewire\Component;

class AnnouncementDetail extends Component
{
    public $announcement_id;
    public $current_announcement;

    public function mount()
    {
        $this->current_announcement = Announcement::find($this->announcement_id);
    }

    public function render()
    {
        return view('livewire.announcements.announcement-detail');
    }
}


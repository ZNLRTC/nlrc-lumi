<?php

namespace App\Livewire\Announcements;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementList extends Component
{
    use WithPagination;

    public $show_filter_modal = false;
    public $is_filtered = false;

    // These properties are used in the view component
    public $filter_is_priority = false;
    public $filter_is_read = false;

    // These properties are used to retain the last checked/unchecked states of the filter checkboxes
    public $filters = [];
    public $filter_is_priority_stored = false;
    public $filter_is_read_stored = false;

    #[On('filtered-announcements')]
    public function render(): View
    {
        $announcements = Auth::user()->trainee->notifications()->where('type', 'announcement-sent');

        if ($this->is_filtered) {
            if ($this->filter_is_priority_stored) {
                // REF: https://laravel.com/docs/11.x/queries#json-where-clauses
                $announcements = $announcements->where('data->is_priority', true);
            }

            if ($this->filter_is_read_stored) {
                $announcements = $announcements->whereNull('read_at');
            }
        }

        // Retain checked/unchecked states of filter checkboxes from the last time the user pressed Filter/Reset Filters
        $this->filter_is_priority = $this->filter_is_priority_stored;
        $this->filter_is_read = $this->filter_is_read_stored;

        $this->filters = [
            'Priority' => $this->filter_is_priority_stored,
            'Unread' => $this->filter_is_read_stored
        ];

        $announcements = $announcements->latest()->paginate(5);

        return view('livewire.announcements.announcement-list', compact('announcements'));
    }

    public function filter_announcements(): void
    {
        $this->filter_is_priority_stored = $this->filter_is_priority;
        $this->filter_is_read_stored = $this->filter_is_read;
        $this->is_filtered = !$this->filter_is_priority && !$this->filter_is_read ? false : true;

        $this->resetPage();

        $this->dispatch('filtered-announcements');
    }

    public function reset_filters($key = false): void
    {
        if (!$key) {
            $this->reset(['filter_is_priority', 'filter_is_read']);
            $this->filter_is_priority_stored = false;
            $this->filter_is_read_stored = false;
        } else if ($key == 'Priority') {
            $this->reset(['filter_is_priority']);
            $this->filter_is_priority_stored = false;
        } else if ($key == 'Unread') {
            $this->reset(['filter_is_read']);
            $this->filter_is_read_stored = false;
        }

        if (!$this->filter_is_priority_stored && !$this->filter_is_read_stored) {
            $this->is_filtered = false;
        }

        $this->resetPage();

        $this->dispatch('filtered-announcements');
    }

    public function set_is_read($notification_id): void
    {
        Auth::user()->trainee->unreadNotifications->where('id', $notification_id)->markAsRead();
    }
}

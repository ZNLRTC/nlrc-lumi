<?php

namespace App\Livewire\Announcements;

use App\Events\ReceiveAnnouncementEvent;
use App\Notifications\AnnouncementNotification;
use App\Mail\SendAnnouncementEmail;
use App\Models\Announcement;
use App\Models\Grouping\Group;
use App\Models\Trainee;
use App\Models\Notification;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;

class SendAnnouncement extends Component
{
    public ?Model $record = null;

    public $active_choice = 'trainees';
    public $groups_dropdown = [];
    public $groups_dropdown_selection = '';
    public $groups_recipients = [];
    public $trainees_dropdown = [];
    public $trainees_dropdown_selection = '';
    public $trainees_recipients = [];
    public $inserted_records_count = 0;
    public $group_recipients_count = 0;
    public $is_priority = false;

    public function change_active_choice()
    {
        $this->active_choice;
    }

    public function add_recipient()
    {
        if ($this->active_choice == 'trainees') {
            // REF: https://laravel.com/docs/11.x/validation#using-closures
            Validator::make(
                ['trainees_dropdown_selection' => $this->trainees_dropdown_selection],
                ['trainees_dropdown_selection' => [
                    'required',
                    function (string $attribute, mixed $value, Closure $fail) {
                        $exploded = explode(',', $value);

                        if (array_key_exists(1, $exploded)) {
                            $trainee = Trainee::select('last_name', 'first_name')
                                ->where('last_name', trim($exploded[0]))
                                ->where('first_name', trim($exploded[1]))
                                ->first();

                            if (!$trainee) {
                                $fail('The trainee does not exist!');
                            }
                        } else {
                            $fail('You must separate the last name and first name with a comma in between');
                        }
                    },
                ]],
            )->validate();

            $names_as_array = explode(',', $this->trainees_dropdown_selection);

            // Sanitize original input and names in array for comparison
            $this->trainees_dropdown_selection = strtolower(trim($names_as_array[0])). ', ' .strtolower(trim($names_as_array[1]));
            $lowercased_trainee_names = array_map(function ($name) {
                return strtolower($name);
            }, $this->trainees_dropdown);

            $index_to_remove = array_search($this->trainees_dropdown_selection, $lowercased_trainee_names);
            $trainee_recipient = $this->trainees_dropdown[$index_to_remove];
            array_push($this->trainees_recipients, $trainee_recipient);
            unset($this->trainees_dropdown[$index_to_remove]);

            $this->reset('trainees_dropdown_selection'); // Clear input
            $this->resetValidation('trainees_dropdown_selection'); // Clear error message

            $this->dispatch('recipient-added');
        } else {
            $trimmed_groups_dropdown_selection = trim(strtoupper($this->groups_dropdown_selection));

            // REF: https://laravel.com/docs/11.x/validation#using-closures
            Validator::make(
                ['groups_dropdown_selection' => $trimmed_groups_dropdown_selection],
                ['groups_dropdown_selection' => 'required|exists:groups,name'],
                ['exists' => 'The group does not exist!'],
            )->validate();

            $this->groups_dropdown_selection = $trimmed_groups_dropdown_selection;
            array_push($this->groups_recipients, $this->groups_dropdown_selection);

            $index_to_remove = array_search($this->groups_dropdown_selection, $this->groups_dropdown);
            if ($index_to_remove || $index_to_remove == 0) {
                unset($this->groups_dropdown[$index_to_remove]);
            }

            $this->reset('groups_dropdown_selection'); // Clear input
            $this->resetValidation('groups_dropdown_selection'); // Clear error message

            $this->dispatch('recipient-added');
        }
    }

    public function remove_recipient($recipient_index)
    {
        if ($this->active_choice == 'trainees') {
            array_push($this->trainees_dropdown, $this->trainees_recipients[$recipient_index]);
            unset($this->trainees_recipients[$recipient_index]);

            $this->trainees_recipients = array_values($this->trainees_recipients); // Re-index recipients
        } else {
            array_push($this->groups_dropdown, $this->groups_recipients[$recipient_index]);
            unset($this->groups_recipients[$recipient_index]);

            $this->groups_recipients = array_values($this->groups_recipients); // Re-index recipients
        }
    }

    public function send_announcement($announcement_id)
    {
        $announcement = Announcement::findOrFail($announcement_id);

        $this->inserted_records_count = 0;
        $this->group_recipients_count = 0;

        if ($this->active_choice == 'trainees') {
            foreach ($this->trainees_recipients as $recipient) {
                $exploded = explode(',', $recipient);
                $trainee = Trainee::select('trainees.id AS id', 'user_id', 'email', 'last_name', 'first_name')
                    ->join('users', 'users.id', 'trainees.user_id')
                    ->where('last_name', trim($exploded[0]))
                    ->where('first_name', trim($exploded[1]))
                    ->first();

                // Queueing mails but is untested. Probably requires actual server to test
                /*
                Mail::to($trainee->email)
                    ->queue(new SendAnnouncementEmail($announcement, $trainee));
                */

                Mail::to($trainee->email)->send(new SendAnnouncementEmail($announcement, $trainee));

                $trainee->notify(new AnnouncementNotification($announcement, $this->is_priority));

                broadcast(new ReceiveAnnouncementEvent($trainee->user_id)); // Trigger an event

                $this->inserted_records_count++;
            }
        } else {
            $this->group_recipients_count = count($this->groups_recipients);

            foreach ($this->groups_recipients as $group_name) {
                // Check for trainee IDs who may have the announcement already sent
                $notifications = Notification::select('notifiable_id')
                    ->where('type', 'announcement-sent')
                    ->where('notifiable_type', 'App\Models\Trainee')
                    ->where('data->announcement_id', $announcement->id)
                    ->get()
                    ->toArray();
                $trainee_ids = array_unique(array_column($notifications, 'notifiable_id'));

                $trainees = Trainee::select('trainees.id AS id', 'user_id', 'email', 'last_name', 'first_name')
                    ->join('group_trainee', 'group_trainee.trainee_id', 'trainees.id')
                    ->join('groups', 'groups.id', 'group_trainee.group_id')
                    ->join('users', 'users.id', 'trainees.user_id')
                    ->where('groups.name', trim($group_name))
                    ->whereNotIn('trainees.id', $trainee_ids)
                    ->get();

                $trainees->each(function ($trainee, $index) use ($announcement) {
                    // Queueing mails but is untested. Probably requires actual server to test
                    /*
                    Mail::to($trainee->email)
                        ->queue(new SendAnnouncementEmail($announcement, $trainee));
                    */

                    Mail::to($trainee->email)->send(new SendAnnouncementEmail($announcement, $trainee));

                    $trainee->notify(new AnnouncementNotification($announcement, $this->is_priority));

                    broadcast(new ReceiveAnnouncementEvent($trainee->user_id)); // Trigger an event

                    $this->inserted_records_count++;
                });
            }
        }

        if ($this->inserted_records_count == 0) {
            $this->dispatch('send-announcement-error');
        } else {
            $this->dispatch('send-announcement-success');
        }

        if ($this->active_choice == 'trainees') {
            $this->trainees_recipients = [];
        } else {
            $this->groups_recipients = [];
        }
    }

    public function mount(?Model $record = null): void
    {
        // Check for trainee IDs who may have the announcement already sent
        $notifications = Notification::where('type', 'announcement-sent')
            ->where('notifiable_type', 'App\Models\Trainee')
            ->where('data->announcement_id', $record->id);
        $trainee_ids = array_unique(array_column($notifications->get()->toArray(), 'notifiable_id'));

        // NOTE: Do not change the order of the CONCAT() as this will affect the logic in send_announcement function
        $trainees = Trainee::selectRaw('trainees.user_id, CONCAT(last_name, ", ", first_name) AS name')
            ->isActive()
            ->whereNotIn('id', $trainee_ids)
            ->get()
            ->toArray();

        $active_groups = Group::isActive()
            ->get();

        // Filter groups with at least 1 active trainee
        $active_groups_with_active_trainees = $active_groups->filter(function ($value) {
            return $value->active_trainee_count > 0;
        })->toArray();

        $this->trainees_dropdown = array_column($trainees, 'name');
        $this->groups_dropdown = array_column($active_groups_with_active_trainees, 'name');
    }

    #[On('recipient-added')]
    public function render()
    {
        return view('filament.custom.send-announcement');
    }
}

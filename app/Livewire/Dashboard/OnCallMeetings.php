<?php

namespace App\Livewire\Dashboard;

use App\Enums\MeetingsOnCallsMeetingStatus;
use App\Events\CancelledOnCallMeetingEvent;
use App\Models\Meetings\MeetingsOnCall;
use App\Models\Meetings\MeetingsOnCallsOptIn;
use App\Models\Trainee;
use App\Models\User;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class OnCallMeetings extends Component
{
    public $next_meeting_on_call;
    public $meetings_on_calls = [];

    public $meeting_on_call_form_modal = false;
    public $confirm_complete_meeting_modal = false;
    public $confirm_cancel_meeting_modal = false;

    public $meeting_on_call_id = 0;
    public $meeting_link;

    public $start_time_meeting_date;
    public $start_time;
    public $start_time_final;
    public $start_time_am_pm;

    public $end_time_meeting_date;
    public $end_time;
    public $end_time_final;
    public $end_time_am_pm;

    public $times = [];
    public $am_pms = ['am', 'pm'];
    public $is_same_start_time_and_end_time = true;
    public $form_type = 'add';
    public $userId;

    public function mount()
    {
        $this->start_time_meeting_date = date('Y-m-d');
        $this->end_time_meeting_date = date('Y-m-d');

        for ($i = 1; $i <= 12; $i++) {
            array_push($this->times, $i. ':00', $i. ':30');
        }
    }

    #[On('on-call-meeting-cancelled')]
    #[On('on-call-meeting-completed')]
    #[On('on-call-meeting-processed')]
    public function render()
    {
        $this->userId = Auth::user()->id; // Don't remove so we don't get an error

        $this->next_meeting_on_call = MeetingsOnCall::where('meeting_status', MeetingsOnCallsMeetingStatus::PENDING)
            ->orderBy('meeting_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->first();

        if ($this->next_meeting_on_call) {
            $base_query = MeetingsOnCall::whereNot('id', $this->next_meeting_on_call['id'])->whereDate('meeting_date', '>=', Carbon::now());

            if (Auth::user()->hasRole('Instructor')) {
                $this->meetings_on_calls = $base_query->clone()
                    ->whereNotIn('meeting_status', [MeetingsOnCallsMeetingStatus::CANCELLED, MeetingsOnCallsMeetingStatus::COMPLETED])
                    ->orderBy('meeting_date', 'ASC')
                    ->orderBy('start_time', 'ASC')
                    ->with('user')
                    ->limit(3)
                    ->get();
            } else {
                $this->meetings_on_calls = $base_query->clone()
                    ->whereIn('meeting_status', [MeetingsOnCallsMeetingStatus::CANCELLED, MeetingsOnCallsMeetingStatus::PENDING])
                    ->orderBy('meeting_date', 'ASC')
                    ->orderBy('start_time', 'ASC')
                    ->with(['meetings_on_calls_opt_ins' => fn ($query) => $query->where('user_id', Auth::user()->id)])
                    ->limit(3)
                    ->get();
            }
        }

        return view('livewire.dashboard.on-call-meetings');
    }

    // Listen to an event
    #[On('echo-private:cancelled-on-call-meeting.{userId},\App\Events\CancelledOnCallMeetingEvent')]
    public function onCancelledOnCallMeeting($event)
    {
        $this->dispatch('on-call-meeting-cancelled');
    }

    public function set_opt_in_notification($meetings_on_call_id)
    {
        if (Auth::user()->trainee) {
            $user_has_opted_in = MeetingsOnCallsOptIn::select(['id', 'is_opt_in'])->where('meetings_on_call_id', $meetings_on_call_id)
                ->where('user_id', Auth::user()->id)
                ->first();

            if (!$user_has_opted_in) {
                MeetingsOnCallsOptIn::create([
                    'meetings_on_call_id' => $meetings_on_call_id,
                    'user_id' => Auth::user()->id
                ]);

                $this->dispatch('on-call-meeting-opted-in');
            } else {
                if ($user_has_opted_in->is_opt_in == 1) {
                    $user_has_opted_in->is_opt_in = 0;
                    $user_has_opted_in->save();

                    $this->dispatch('on-call-meeting-opted-out');
                } else {
                    $user_has_opted_in->is_opt_in = 1;
                    $user_has_opted_in->save();

                    $this->dispatch('on-call-meeting-opted-in');
                }
            }
        }
    }

    public function toggle_same_start_time_and_end_time()
    {
        if ($this->is_same_start_time_and_end_time) {
            $this->end_time_meeting_date = $this->start_time_meeting_date;
        }
    }

    // Lifecycle hook based on property $start_time_meeting_date
    public function updatedStartTimeMeetingDate()
    {
        if ($this->start_time_meeting_date != $this->end_time_meeting_date && $this->is_same_start_time_and_end_time) {
            $this->is_same_start_time_and_end_time = false;
        }
    }

    // Lifecycle hook based on property $end_time_meeting_date
    public function updatedEndTimeMeetingDate()
    {
        if ($this->start_time_meeting_date != $this->end_time_meeting_date && $this->is_same_start_time_and_end_time) {
            $this->is_same_start_time_and_end_time = false;
        }
    }

    public function set_form_type($form_type, $meeting_on_call_id = null)
    {
        $this->form_type = $form_type;

        if ($this->form_type == 'edit') {
            $this->meeting_on_call_id = $meeting_on_call_id;
            $meeting_on_call = MeetingsOnCall::find($this->meeting_on_call_id);

            $this->meeting_link = $meeting_on_call['meeting_link'];

            $this->start_time_meeting_date = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['start_time'], 'Y-m-d');
            $this->start_time = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['start_time'], 'g:i');
            $this->start_time_am_pm = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['start_time'], 'a');
            $this->end_time_meeting_date = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['end_time'], 'Y-m-d');
            $this->end_time = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['end_time'], 'g:i');
            $this->end_time_am_pm = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['end_time'], 'a');

            $this->is_same_start_time_and_end_time = $this->start_time_meeting_date == $this->end_time_meeting_date;
        } else {
            $this->reset();

            $this->meeting_on_call_id = 0;
            $this->start_time_meeting_date = date('Y-m-d');
            $this->end_time_meeting_date = date('Y-m-d');
            $this->is_same_start_time_and_end_time = true;
        }

        $this->meeting_on_call_form_modal = true;

        $this->times = [];

        for ($i = 1; $i <= 12; $i++) {
            array_push($this->times, $i. ':00', $i. ':30');
        }
    }

    public function complete_meeting_on_call()
    {
        $this->next_meeting_on_call->update(['meeting_status' => MeetingsOnCallsMeetingStatus::COMPLETED]);

        $this->confirm_complete_meeting_modal = false;

        $this->dispatch('on-call-meeting-completed');
    }

    #[On('on-call-meeting-id-set')]
    public function set_meeting_on_call_id($meeting_on_call_id)
    {
        $this->meeting_on_call_id = $meeting_on_call_id;

        $this->confirm_cancel_meeting_modal = true;
    }

    public function cancel_meeting_on_call($meeting_on_call_id)
    {
        MeetingsOnCall::where('id', $meeting_on_call_id)
            ->update(['meeting_status' => MeetingsOnCallsMeetingStatus::CANCELLED]);

        $this->confirm_cancel_meeting_modal = false;

        // TODO: Maybe we can use the opt-in notifications to trainees who opted in for the notification of the on-call meeting to be cancelled?
        Trainee::select(['trainees.id AS trainees_id', 'user_id', 'first_name', 'middle_name', 'last_name'])->join('users', 'users.id', 'trainees.user_id')
            ->isActive()
            ->where('users.notification_settings->meetings_on_call', 1)
            ->chunk(250, function (Collection $recipients) {
                // TODO: Do we need to notify them and add a record to the notifications table?
                // Commented code below just in case we need to implement it (also need to create the notification class)
                // Illuminate\Support\Facades\Notification::send($recipients, new MeetingsOnCallCancelledNotification($meetings_on_call_record));

                foreach ($recipients as $trainee) {
                    broadcast(new CancelledOnCallMeetingEvent($trainee->user_id)); // Trigger an event
                }
            });

        $this->dispatch('on-call-meeting-cancelled');
    }

    public function process_meeting_on_call_form()
    {
        $this->validate([
            'meeting_link' => ['required', 'min:25', 'max:64', 'starts_with:https://meet.google.com/'],
            'start_time_meeting_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $is_behind = MeetingsOnCall::is_current_time_behind_instructor_current_time($this->start_time_am_pm, $value, $this->start_time_meeting_date, Auth::user()->timezone);

                    if ($is_behind) {
                        $fail('The start time field cannot be less than the current time of your timezone.');
                    }
                }
            ],
            'start_time_am_pm' => ['required'],
            'end_time_meeting_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_time_meeting_date'],
            'end_time' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($this->start_time && $this->end_time) {
                        // Parse inputs based on the instructor's timezone
                        $this->start_time_final = MeetingsOnCall::parse_meeting_times_to_utc($this->start_time_am_pm, $this->start_time, $this->start_time_meeting_date);
                        $this->end_time_final = MeetingsOnCall::parse_meeting_times_to_utc($this->end_time_am_pm, $this->end_time, $this->end_time_meeting_date);

                        // Check if they overlap with existing on-call meetings between start_time and end_time fields
                        $has_overlapping_meeting_on_call = MeetingsOnCall::where('user_id', Auth::user()->id)
                            ->whereDate('meeting_date', $this->start_time_meeting_date)
                            ->whereNot('meeting_status', MeetingsOnCallsMeetingStatus::CANCELLED)
                            ->where('start_time', '<', $this->end_time_final->format('Y-m-d H:i:s'))
                            ->where('end_time', '>', $this->start_time_final->format('Y-m-d H:i:s'));

                        if ($this->meeting_on_call_id != 0 && $this->form_type == 'edit') {
                            $has_overlapping_meeting_on_call = $has_overlapping_meeting_on_call->whereNot('id', $this->meeting_on_call_id)
                                ->exists();
                        } else {
                            $has_overlapping_meeting_on_call = $has_overlapping_meeting_on_call->exists();
                        }

                        if ($this->end_time_final < $this->start_time_final) {
                            $fail('The end time field cannot be less than the start time.');
                        } else if ($this->end_time_final == $this->start_time_final) {
                            $fail('The end time field cannot be equal to the start time.');
                        } else if ($this->start_time_final->diff($this->end_time_final)->format('%H:%I:%S') >= '02:30:00') {
                            $fail('The start time and end time fields should only run for 2 hours or less.');
                        } else if ($has_overlapping_meeting_on_call) {
                            $fail('The start time and end time fields cannot overlap with existing on-call meeting(s) for ' .$this->start_time_meeting_date. '.');
                        }
                    }
                },
            ],
            'end_time_am_pm' => ['required'],
        ], ['starts_with' => 'The :attribute field must start with a valid Google Meet link.']);

        // NOTE: meeting_date, start_time, and end_time fields are saved in UTC timezone in the DB
        if ($this->start_time_final->format('Y-m-d') != $this->start_time_meeting_date) {
            $this->start_time_meeting_date = $this->start_time_final->format('Y-m-d');
        }

        MeetingsOnCall::updateOrCreate(
            ['id' => $this->meeting_on_call_id],
            [
                'user_id' => Auth::user()->id,
                'meeting_link' => $this->meeting_link,
                'meeting_date' => $this->start_time_meeting_date,
                'start_time' => $this->start_time_final->format('Y-m-d H:i:s'),
                'end_time' => $this->end_time_final->format('Y-m-d H:i:s'),
            ]
        );

        $this->meeting_on_call_form_modal = false;

        $this->dispatch('on-call-meeting-processed');
    }
}

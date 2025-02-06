<?php

namespace App\Console\Commands;

use App\Models\Trainee;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Meetings\MeetingsOnCall;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\MeetingsOnCallsMeetingStatus;
use App\Notifications\MeetingsOnCallNotification;

class NotifyAboutUpcomingOnCallMeeting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'on-call-meetings:notify-about-upcoming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify trainees for upcoming on-call meetings 30 minutes prior to its start time';
    // NOTE: MIGHT REFACTOR THIS AS A BROADCAST EVENT INSTEAD OF A SCHEDULER?
    // TODO: USE BROADCASTING TO SEND REAL-TIME NOTIFICATIONS TO TRAINEES;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        $this->info("Today's time is: {$today->format('Y-m-d H:i:s A')}");
    
        $meetings_on_calls_starting_in_30_mins = MeetingsOnCall::where('start_time', '<=', $today->addMinutes(30))
            ->where('meeting_status', MeetingsOnCallsMeetingStatus::PENDING)
            ->get();
    
        $this->info("Count of on-call meetings that will start in the next 30 mins: {$meetings_on_calls_starting_in_30_mins->count()}");

        if ($meetings_on_calls_starting_in_30_mins->isEmpty()) {
            $this->info("There are currently no on-call meetings that will start in the next 30 mins.");
            return;
        }
    
        // I added eager loading (i.e. ->with) to load notifications without N+1 issues and chunks to not choke the db. If this doesn't seem good, the old code is commented out below. --Mikko
        Trainee::select(['trainees.id AS id', 'first_name', 'last_name'])
            ->join('users', 'users.id', '=', 'trainees.user_id')
            ->where('notification_settings->meetings_on_call', 1) // Trainees who didn't opt-out of on-call meeting notifications
            ->whereHas('activeGroup', fn (Builder $query) => $query->whereNot('group_id', 1)) // Not in beginner's course Kyl mä hoidan
            ->with('notifications')
            ->chunk(100, function ($trainees) use ($meetings_on_calls_starting_in_30_mins) {
                foreach ($meetings_on_calls_starting_in_30_mins as $meeting) {
                    foreach ($trainees as $trainee) {
                        $traineeHasNotifForOnCallMeeting = $trainee->notifications()
                            ->where('type', 'meetings-on-call-sent')
                            ->where('data->meetings_on_call_id', $meeting->id)
                            ->first();

                        if ($traineeHasNotifForOnCallMeeting) {
                            $this->info("Trainee {$trainee->first_name} {$trainee->last_name} has already received the notification for meetings_on_call id: {$meeting->id}");
                        } else {
                            $this->info("Trainee {$trainee->first_name} {$trainee->last_name} hasn't received the notification yet. Notifying...");

                            $trainee->notify(new MeetingsOnCallNotification($meeting));
                        }
                    }
                }
            });

        // $today = Carbon::now();

        // echo "\nLOG: Today's time is: " .$today->format('Y-m-d H:i:s A');
    
        // $meetings_on_calls_starting_in_30_mins = MeetingsOnCall::where('start_time', '<=', $today->addMinutes(30))
        //     ->where('meeting_status', MeetingsOnCallsMeetingStatus::PENDING)
        //     ->get();
    
        // echo "\nLOG: Today's time is (added 30 minutes): " .$today->format('Y-m-d H:i:s A');
        // echo "\nLOG: Count of on-call meetings that will start in the next 30 mins: " .$meetings_on_calls_starting_in_30_mins->count();
    
        // if ($meetings_on_calls_starting_in_30_mins->count() > 0) {
        //     $trainees_in_active_training = Trainee::select(['trainees.id AS id', 'first_name', 'last_name'])
        //         ->join('users', 'users.id', '=', 'trainees.user_id')
        //         ->where('notification_settings->meetings_on_call', 1) // Trainees who didn't opt-out of on-call meeting notifications
        //         ->whereHas('activeGroup', fn (Builder $query) => $query->whereNot('group_id', 1)) // Not in beginner's course Kyl mä hoidan
        //         ->get();
    
        //     echo "\nLOG: Count of trainees not in Kyl ma hoidan course with activated on-call meeting notifs: " .$trainees_in_active_training->count();
    
        //     $count_trainees_notified_for_incoming_meeting_on_call = 0;
    
        //     foreach ($meetings_on_calls_starting_in_30_mins as $meetings_on_call) {
        //         foreach ($trainees_in_active_training as $trainee) {
        //             $trainee_has_notif_for_on_call_meeting = $trainee->notifications()
        //                 ->where('type', 'meetings-on-call-sent')
        //                 ->where('data->meetings_on_call_id', $meetings_on_call->id)
        //                 ->first();
    
        //             if ($trainee_has_notif_for_on_call_meeting) {
        //                 echo "\nLOG: Trainee " .$trainee->first_name. " " .$trainee->last_name. " has already received the notification for meetings_on_call id: " .$meetings_on_call->id;
        //             } else {
        //                 echo "\nLOG: Trainee " .$trainee->first_name. " " .$trainee->last_name. " hasn't received the notification yet. Notifying...";
    
        //                 $trainee->notify(new MeetingsOnCallNotification($meetings_on_call));
    
        //                 $count_trainees_notified_for_incoming_meeting_on_call++;
        //             }
        //         }
        //     }
    
        //     echo "\nLOG: Count of trainees notified: " .$count_trainees_notified_for_incoming_meeting_on_call. "\n\n";
        // } else {
        //     echo "\nLOG: There are currently no on-call meetings that will start in the next 30 mins.\n\n";
        // }
    }
}

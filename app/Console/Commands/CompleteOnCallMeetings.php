<?php

namespace App\Console\Commands;

use App\Enums\MeetingsOnCallsMeetingStatus;
use App\Models\Meetings\MeetingsOnCall;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CompleteOnCallMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'on-call-meetings:auto-complete-meetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically set on-call meetings to Completed so that the queue would move up with the next on-call meeting, in case the instructor forgot to complete the meeting 30 minutes after the meeting(s) end';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        $this->info("Current time is: " . $today->format('Y-m-d H:i:s A'));

        $meetings = MeetingsOnCall::select(['id', 'meeting_status'])->where('end_time', '<', $today)
            ->where('meeting_status', MeetingsOnCallsMeetingStatus::PENDING)
            ->get();

        if ($meetings->isEmpty()) {
            $this->info("There are currently no on-call meetings whose meeting_status field is pending and end_time field is past today's time.");

            return;
        }

        foreach ($meetings as $meeting) {
            $meeting->update(['meeting_status' => MeetingsOnCallsMeetingStatus::COMPLETED]);

            $this->info("Meeting status for on-call meeting ID {$meeting->id} is now set to completed.");
        }

        $this->info("Completed processing on-call meetings.");
        
        // Maybe this should be saved to the app log too?
        Log::info("Completed processing on-call meetings.");
    }
}

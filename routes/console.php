<?php

use App\Console\Commands\CheckTraineeStudiesStatus;
use App\Console\Commands\CompleteOnCallMeetings;
use App\Console\Commands\NotifyAboutUpcomingOnCallMeeting;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckTraineeStudiesStatus::class)->saturdays()->at('1:00');
Schedule::command(CompleteOnCallMeetings::class)->everyThirtyMinutes();
Schedule::command(NotifyAboutUpcomingOnCallMeeting::class)->everyThirtyMinutes();
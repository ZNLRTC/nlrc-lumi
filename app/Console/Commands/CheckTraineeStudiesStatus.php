<?php

namespace App\Console\Commands;

use App\Models\Flag\Flag;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\Flag\FlagTrainee;
use App\Models\Meetings\Meeting;
use App\Models\Planner\PlannerWeek;
use App\Models\Meetings\MeetingTrainee;
use App\Models\Planner\PlannerWeeklySchedule;

class CheckTraineeStudiesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainee:check-trainee-studies-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of active trainees\' studies in relation to the schedule of their gruop and flag if behind schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Assuming this is run every Saturday and the meeting cycle ends on Friday, this checks the meetings for the week it's actually run.
        $lastMeetingWeekStart = Carbon::now()->startOfWeek();

        $weekNumber = $lastMeetingWeekStart->isoWeek();
        $year = $lastMeetingWeekStart->year;

        $plannerWeek = PlannerWeek::where('number', $weekNumber)
            ->where('year', $year)
            ->where('finalized', true)
            ->first();

        if ($plannerWeek) {
            PlannerWeeklySchedule::with('group.trainees')
                ->where('planner_week_id', $plannerWeek->id)
                ->chunk(100, function ($schedules) {
                    foreach ($schedules as $schedule) {
                        foreach ($schedule->group->activeTrainees as $trainee) {
                            // 1) Check and remove any existing flags for completed meetings in general
                            $existingFlags = FlagTrainee::where('trainee_id', $trainee->id)
                                ->whereHas('flag', function ($query) {
                                    $query->where('name', 'Missing a meeting');
                                })
                                ->get();

                            foreach ($existingFlags as $existingFlag) {
                                $meetingTrainee = MeetingTrainee::where('trainee_id', $trainee->id)
                                    ->where('meeting_id', $existingFlag->meeting_id)
                                    ->where('meeting_status_id', 1) // = Completed
                                    ->first();

                                if ($meetingTrainee) {
                                    $existingFlag->update([
                                        'active' => false,
                                        'flagged_by_system' => true,
                                    ]);
                                }
                            }

                            // 2) Check for new flags for missing meetings last week
                            foreach ($schedule->meetings as $meetingId) {
                                $meetingTrainee = MeetingTrainee::where('trainee_id', $trainee->id)
                                    ->where('meeting_id', $meetingId)
                                    ->where('meeting_status_id', 1) // = Completed
                                    ->first();

                                $meeting = Meeting::find($meetingId);

                                if (!$meetingTrainee) {
                                    // No double flagging
                                    $existingFlag = FlagTrainee::where('trainee_id', $trainee->id)
                                        ->where('meeting_id', $meetingId)
                                        ->first();

                                    if (!$existingFlag) {
                                        $flag = Flag::firstOrCreate([
                                            'name' => 'Missing a meeting',
                                            'description' => 'The trainee is missing a meeting.',
                                            'visible_to_trainee' => 0,
                                            'flag_type_id' => 2, // = Training team
                                        ]);

                                        FlagTrainee::create([
                                            'trainee_id' => $trainee->id,
                                            'flag_id' => $flag->id,
                                            'meeting_id' => $meetingId,
                                            'flagged_by' => null,
                                            'flagged_by_system' => true,
                                            'active' => true,
                                            'description' => "Missing {$meeting->description}",
                                            'internal_notes' => "Missing meeting: {$meeting->description}. Flagged by the system.",
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                });
        }

        $this->info('Trainee studies check completed.');
    }
}

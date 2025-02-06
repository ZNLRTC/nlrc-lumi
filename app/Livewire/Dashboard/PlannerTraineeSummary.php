<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Courses\Unit;
use App\Models\Meetings\Meeting;
use App\Models\Planner\PlannerWeek;
use Illuminate\Support\Facades\Auth;
use App\Models\Planner\PlannerWeeklySchedule;

class PlannerTraineeSummary extends Component
{
    public $currentWeeklySchedule;
    public $nextWeeklySchedule;
    public $twoWeeksLaterWeeklySchedule;

    public $currentUnits = [];
    public $currentMeetings = [];

    public $nextUnits = [];
    public $nextMeetings = [];

    public $twoWeeksLaterUnits = [];
    public $twoWeeksLaterMeetings = [];

    public $currentDateRange;
    public $nextDateRange;
    public $twoWeeksLaterDateRange;
    
    public $groupName;

    public function mount()
    {
        $this->fetchWeeklySchedules();
        $this->groupName = Auth::user()->trainee->activeGroup->group->group_code ?? 'No group'; // Don't change the null spelling as it's used in an @if check in the view
    }

    public function fetchWeeklySchedules()
    {
        $this->fetchWeeklyScheduleData(0, 'current');
        $this->fetchWeeklyScheduleData(1, 'next');
        $this->fetchWeeklyScheduleData(2, 'twoWeeksLater');
    }

    private function fetchWeeklyScheduleData($weekOffset, $scheduleType)
    {
        $currentDate = Carbon::now()->addWeeks($weekOffset);
        $weekNumber = $currentDate->isoWeek();
        $year = $currentDate->year;

        $activeGroup = Auth::user()->trainee->activeGroup;

        $plannerWeek = PlannerWeek::where('number', $weekNumber)
            ->where('year', $year)
            ->where('finalized', true)
            ->first();

        if ($plannerWeek) {
            $weeklySchedule = PlannerWeeklySchedule::with('plannerWeek')
                ->where('group_id', $activeGroup->group_id)
                ->where('planner_week_id', $plannerWeek->id)

                ->first();

            if ($weeklySchedule) {
                $units = Unit::with('course')
                    ->whereIn('id', $weeklySchedule->units)
                    ->get();
                $meetings = Meeting::whereIn('id', $weeklySchedule->meetings)->get();
                $dateRange = $this->formatDateRange($plannerWeek->start_date, $plannerWeek->end_date);

                $this->{$scheduleType . 'WeeklySchedule'} = $weeklySchedule;
                $this->{$scheduleType . 'Units'} = $units;
                $this->{$scheduleType . 'Meetings'} = $meetings;
                $this->{$scheduleType . 'DateRange'} = $dateRange;
            }
        }
    }

    // Removes the month if it's the same for start and end dates
    private function formatDateRange($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $startMonth = $start->format('M');
        $startDay = $start->format('j');
        $endMonth = $end->format('M');
        $endDay = $end->format('j');

        return $startMonth === $endMonth
            ? "{$startMonth} {$startDay}–{$endDay}"
            : "{$startMonth} {$startDay}–{$endMonth} {$endDay}";
    }

    public function render()
    {
        return view('livewire.dashboard.planner-trainee-summary');
    }
}

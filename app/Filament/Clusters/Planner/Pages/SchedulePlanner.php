<?php

namespace App\Filament\Clusters\Planner\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;
use App\Models\Courses\Unit;
use App\Models\Grouping\Group;
use Illuminate\Support\Carbon;
use App\Models\Meetings\Meeting;
use Livewire\Attributes\Computed;
use App\Filament\Clusters\Planner;
use App\Models\Planner\PlannerWeek;
use Illuminate\Support\Facades\Cache;
use App\Models\Planner\PlannerWeeklySchedule;
use App\Models\Planner\PlannerGroupCurriculum;
use App\Models\Planner\PlannerCurriculumContent;

class SchedulePlanner extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.clusters.planner.pages.schedule-planner';

    protected static ?string $cluster = Planner::class;

    public bool $showDeleteModal = false;
    public ?int $weekToDelete = null;

    public $startDate;
    public $endDate;

    public array $units = [];
    public array $meetings = [];

    public int $sortByGroupNameState = 0; // 0: id, 1: asc, 2: desc
    public ?int $sortByWeekId = null;

    public function mount()
    {
        $currentDate = Carbon::now();
        $this->startDate = $currentDate->copy()->subWeek()->format('Y-m-d');
        $this->endDate = $currentDate->copy()->addWeeks(2)->format('Y-m-d');

        $this->units = Unit::all()->pluck('name', 'id')->toArray();
        $this->meetings = Meeting::all()->pluck('description', 'id')->toArray();
    }

    public function niceWeekDates($week)
    {
        $startDate = Carbon::parse($week->start_date);
        $endDate = Carbon::parse($week->end_date);
        $sameMonth = $startDate->format('M') === $endDate->format('M');
        $sameYear = $startDate->format('Y') === $endDate->format('Y');

        if ($sameMonth && $sameYear) {
            return $startDate->format('M j') . '–' . $endDate->format('j, Y');
        } elseif ($sameYear) {
            return $startDate->format('M j') . '–' . $endDate->format('M j, Y');
        } else {
            return $startDate->format('M j, Y') . '–' . $endDate->format('M j, Y');
        }
    }

    public function toggleSortByGroupName()
    {
        $this->sortByWeekId = null;
        $this->sortByGroupNameState = ($this->sortByGroupNameState + 1) % 3;
    }

    public function sortByWeek($weekId)
    {
        $this->sortByGroupNameState = 0;
        $this->sortByWeekId = $weekId;
    }

    #[Computed]
    public function Groups()
    {
        $groups = Group::where('active', true)
            ->whereNot('name', 'Kyl mä hoidan')
            ->get();

        if ($this->sortByGroupNameState === 1) {
            $groups = $groups->sortBy('group_code');
        } elseif ($this->sortByGroupNameState === 2) {
            $groups = $groups->sortByDesc('group_code');
        }

        if ($this->sortByWeekId) {
            $groups = $groups->sortBy(function ($group) {
                $schedule = PlannerWeeklySchedule::where('group_id', $group->id)
                    ->where('planner_week_id', $this->sortByWeekId)
                    ->first();
                if ($schedule && isset($schedule->units[0])) {
                    return (int)$schedule->units[0];
                }
                return PHP_INT_MAX;
            });
        }

        return $groups;
    }

    #[Computed]
    public function Weeks()
    {
        return PlannerWeek::whereBetween('start_date', [$this->startDate, $this->endDate])->get();
    }

    #[Computed]
    public function Schedules()
    {
        return PlannerWeeklySchedule::with(['group', 'plannerWeek', 'curriculumContent'])
            ->whereIn('group_id', $this->groups->pluck('id'))
            ->whereIn('planner_week_id', $this->weeks->pluck('id'))
            ->get()
            ->groupBy(['group_id', 'planner_week_id']);
    }

    // Called in the view
    #[Computed]
    public function filteredUnitsAndMeetings($groupId, $weekId)
    {
        $filteredUnits = [];
        $filteredMeetings = [];

        if (isset($this->schedules[$groupId]) && isset($this->schedules[$groupId][$weekId])) {
            $schedules = $this->schedules[$groupId][$weekId];
    
            foreach ($schedules as $schedule) {
                foreach ($schedule->units as $unitId) {
                    if (isset($this->units[$unitId])) {
                        $filteredUnits[$unitId] = $this->units[$unitId];
                    }
                }
    
                foreach ($schedule->meetings as $meetingId) {
                    if (isset($this->meetings[$meetingId])) {
                        $filteredMeetings[$meetingId] = $this->meetings[$meetingId];
                    }
                }
            }
        }

        return [
            'units' => $filteredUnits,
            'meetings' => $filteredMeetings,
        ];
    }

    // Called in the view
    #[Computed]
    public function getScheduleForGroupAndWeek($groupId, $weekId)
    {
        if (isset($this->schedules[$groupId]) && isset($this->schedules[$groupId][$weekId])) {
            return $this->schedules[$groupId][$weekId]->first() ?? null;
        }
        return null;
    }

    #TODO: This does not take into account that for group meetings, the trainee count is 1/6 of the group size.
    #[On('countUpdated')]
    #[Computed]
    public function weeklyTraineeCounts()
    {
        $weeklyTraineeCounts = [];

        foreach ($this->weeks as $week) {
            $weekId = $week->id;
            $weeklyTraineeCounts[$weekId] = 0;

            foreach ($this->groups as $group) {
                $groupId = $group->id;

                if (isset($this->schedules[$groupId]) && isset($this->schedules[$groupId][$weekId])) {
                    $schedules = $this->schedules[$groupId][$weekId];

                    foreach ($schedules as $schedule) {
                        $weeklyTraineeCounts[$weekId] += $schedule->trainees ?? 0;
                    }
                }
            }
        }

        return $weeklyTraineeCounts;
    }

    #TODO: This always reloads all weeks when it could just reload the updated weeks. Changing this requires changing the key in the loop in the view and then only updating specific weeks.
    #[On('contentUpdated')]
    #[On('scheduleExtended')]
    public function handleScheduleExtended()
    {
        $this->mount();
    }

    // Certain buttons are only shown for current or upcoming weeks in the view
    public function isNotInThePast($week)
    {
        $currentDate = Carbon::now();
        $weekStartDate = Carbon::parse($week->start_date);
    
        return $weekStartDate->isSameWeek($currentDate) || $weekStartDate->isAfter($currentDate);
    }

    public function finalizeWeek($weekId)
    {
        $week = PlannerWeek::findOrFail($weekId);
        $week->finalized = !$week->finalized; // Toggle
        $week->save();

        // Switch trainee count between static and dynamic
        $schedules = PlannerWeeklySchedule::where('planner_week_id', $weekId)->get();
        $groups = Group::whereIn('id', $schedules->pluck('group_id'))->get()->keyBy('id');

        foreach ($schedules as $schedule) {
            if ($week->finalized) {
                $group = $groups->get($schedule->group_id);
    
                // Some weeks don't have meetings and need 0 trainees saved here
                if (empty($schedule->meetings) || !in_array($schedule->content_type->value, ['default', 'meeting_only', 'custom_content'])) {
                    $schedule->trainees = 0;
                } else {
                    $schedule->trainees = $group->active_trainee_count;
                }
            } else {
                $schedule->trainees = null;
            }
            $schedule->save();

            Cache::forget("trainee_count_{$schedule->group_id}");
        }
    }

    public function extendSchedules($weekId)
    {
        $week = PlannerWeek::findOrFail($weekId);

        $nextWeekDate = Carbon::parse($week->start_date)->addWeek();
        $nextWeekNumber = $nextWeekDate->isoWeek();
        $nextYear = $nextWeekDate->year;

        $nextWeekStartDate = $nextWeekDate->copy()->startOfWeek();
        $nextWeekEndDate = $nextWeekDate->copy()->endOfWeek();
    
        $nextWeek = PlannerWeek::firstOrCreate(
            [
                'number' => $nextWeekNumber,
                'year' => $nextYear,
            ],
            [
                'start_date' => $nextWeekStartDate,
                'end_date' => $nextWeekEndDate,
            ]
        );

        $groups = $this->Groups();

        foreach ($groups as $group) {
            $activeCurriculumIds = PlannerGroupCurriculum::where('group_id', $group->id)
                ->where('is_active', true)
                ->pluck('planner_curriculum_id');
    
            if ($activeCurriculumIds->isNotEmpty()) {
                $scheduleExists = PlannerWeeklySchedule::where('group_id', $group->id)
                    ->where('planner_week_id', $nextWeek->id)
                    ->exists();
    
                if (!$scheduleExists) {
                    $currentSchedule = PlannerWeeklySchedule::where('group_id', $group->id)
                        ->where('planner_week_id', $weekId)
                        ->first();

                    $currentContent = $currentSchedule ? PlannerCurriculumContent::find($currentSchedule->planner_curriculum_contents_id) : null;
                    $nextContentSort = $currentContent ? $currentContent->sort + 1 : 1;
                        
                    $nextContent = PlannerCurriculumContent::whereIn('planner_curriculum_id', $activeCurriculumIds)
                        ->where('sort', $nextContentSort)
                        ->first();
                        
                    // dd($nextContent);
                    if ($nextContent) {
                        $units = $nextContent->units()->pluck('units.id')->unique()->toArray();
                        $meetings = $nextContent->meetings()->pluck('meetings.id')->unique()->toArray();
    
                        PlannerWeeklySchedule::create([
                            'group_id' => $group->id,
                            'planner_week_id' => $nextWeek->id,
                            'planner_curriculum_contents_id' => $nextContent->id,
                            'units' => $units,
                            'meetings' => $meetings,
                            'content_type' => $nextContent->content_type,
                            'show_custom_content' => $nextContent->show_custom_content,
                            'custom_content' => $nextContent->custom_content,
                        ]);
                    }
                }
            }
        }
    }

    public function confirmDelete($weekId)
    {
        $this->weekToDelete = $weekId;
        $this->dispatch('open-modal', id: 'deleteConfirmationModal');
    }
    
    public function deleteWeek()
    {
        $groups = $this->Groups();

        foreach ($groups as $group) {
            PlannerWeeklySchedule::where('group_id', $group->id)
                ->where('planner_week_id', $this->weekToDelete)
                ->delete();

                $this->dispatch('weekDeleted', weekId: $this->weekToDelete);
            }

        $this->dispatch('close-modal', id: 'deleteConfirmationModal');
    }

}

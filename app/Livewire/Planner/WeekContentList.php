<?php

namespace App\Livewire\Planner;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Models\Grouping\Group;
use Illuminate\Support\Carbon;
use App\Enums\Planner\ContentType;
use App\Models\Planner\PlannerWeek;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Contracts\HasForms;
use App\Models\Planner\PlannerWeeklySchedule;
use App\Models\Planner\PlannerGroupCurriculum;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Planner\PlannerCurriculumContent;

// The parent component is in /app/Filament/Clusters/Planner/Pages/SchedulePlanner.php

#[Lazy(isolate: false)] 
class WeekContentList extends Component implements HasForms
{
    use InteractsWithForms;

    public int $groupId;
    public int $weekId;
    public ?int $contentId;
    public bool $finalized;

    public $customContent;
    public bool $showCustomContent = false;
    public ?ContentType $contentType;
    public $activeTraineeCount;
    public array $unitNames = [];
    public array $meetingNames = [];
    public ?PlannerWeeklySchedule $schedule = null;

    public function mount($groupId, $weekId, $finalized, $unitNames, $meetingNames, $schedule)
    {
        $this->groupId = $groupId;
        $this->weekId = $weekId;
        $this->finalized = $finalized;
        $this->unitNames = $unitNames;
        $this->meetingNames = $meetingNames;
        $this->schedule = $schedule;

        $this->loadScheduleAndTraineeCount();
    }

    // #[On('contentUpdated')]
    public function loadScheduleAndTraineeCount()
    {
        if ($this->schedule) {
            $this->contentId = $this->schedule->planner_curriculum_contents_id;
            $this->customContent = $this->schedule->custom_content;
            $this->showCustomContent = $this->schedule->show_custom_content;
            $this->contentType = $this->schedule->content_type;
            $this->activeTraineeCount = $this->schedule->trainees !== null ? $this->schedule->trainees : $this->getCachedTraineeCount();
        } else {
            $this->contentId = null;
            $this->customContent = '';
            $this->showCustomContent = false;
            $this->contentType = ContentType::DEFAULT;
            $this->activeTraineeCount = $this->getCachedTraineeCount();
        }
    }

    private function resetProperties()
    {
        $this->contentId = null;
        $this->customContent = '';
        $this->showCustomContent = false;
        $this->contentType = ContentType::DEFAULT;
        $this->schedule = null;
    }

    private function getCachedTraineeCount()
    {
        if (empty($this->meetingNames) || !in_array($this->contentType->value, ['default', 'meeting_only', 'custom_content'])) {
            return 0;
        }
        
        return Cache::remember("trainee_count_{$this->groupId}", 3600, function () {
            return Group::find($this->groupId)->active_trainee_count;
        });
    }

    public function saveTraineeCount()
    {
        $schedule = PlannerWeeklySchedule::where('group_id', $this->groupId)
            ->where('planner_week_id', $this->weekId)
            ->first();

        if ($schedule) {
            $schedule->trainees = $this->activeTraineeCount;
            $schedule->save();
        }
    }

    public function extendSchedule()
    {
        $week = PlannerWeek::findOrFail($this->weekId);

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

        $activeCurriculumIds = PlannerGroupCurriculum::where('group_id', $this->groupId)
            ->where('is_active', true)
            ->pluck('planner_curriculum_id');

        if ($activeCurriculumIds->isNotEmpty()) {
            $scheduleExists = PlannerWeeklySchedule::where('group_id', $this->groupId)
                ->where('planner_week_id', $nextWeek->id)
                ->exists();

            if (!$scheduleExists) {
                $currentContent = PlannerCurriculumContent::find($this->contentId);
                
                $nextContentSort = $currentContent ? $currentContent->sort + 1 : 1;

                $nextContent = PlannerCurriculumContent::whereIn('planner_curriculum_id', $activeCurriculumIds)
                    ->where('sort', $nextContentSort)
                    ->first();

                if ($nextContent) {
                    $units = $nextContent->units()->pluck('units.id')->unique()->toArray();
                    $meetings = $nextContent->meetings()->pluck('meetings.id')->unique()->toArray();

                    PlannerWeeklySchedule::create([
                        'group_id' => $this->groupId,
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

        $this->dispatch('scheduleExtended');
    }

    #[On('weekDeleted')]
    public function handleWeekDeleted($weekToDelete = null)
    {
        if ($weekToDelete !== null && $weekToDelete == $this->weekId) {
            $this->resetProperties();
        }
    }

    public function deleteThisWeek()
    {
        $this->dispatch('confirmWeekDelete', ['weekId' => $this->weekId, 'groupId' => $this->groupId]);
    }

    public function openForm()
    {
        $this->dispatch('openEditForm', ['weekId' => $this->weekId, 'groupId' => $this->groupId]);
    }

    public function confirmDelete()
    {
        $this->dispatch('open-modal', 'confirm-delete-week-info');
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class='w-72 p-2 flex flex-col'>
                <div class='w-36 h-6 mb-2 bg-gray-200 dark:bg-gray-700 rounded animate-pulse'></div>
                <div class='w-52 h-4 mb-10 bg-gray-200 dark:bg-gray-700 rounded animate-pulse'></div>
            </div>
        HTML;
    }
    public function render()
    {
        return view('livewire.planner.week-content-list');
    }
}
<?php

namespace App\Livewire\Planner;

use Filament\Forms\Get;
use Livewire\Component;
use Filament\Forms\Form;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Enums\Planner\ContentType;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use App\Models\Planner\PlannerWeeklySchedule;
use App\Models\Planner\PlannerGroupCurriculum;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Planner\PlannerCurriculumContent;

class WeekContentForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?int $groupId = null;
    public int $weekId;

    public ?int $contentId = null;
    public ?string $customContent;
    public ?bool $showCustomContent = false;
    public ?array $data = [];

    // Units and meetings this week has
    public ?array $units = [];
    public ?array $meetings = [];

    // All units and meetings
    public array $unitNames = [];
    public array $meetingNames = [];

    public $contentType;

    public ?PlannerWeeklySchedule $schedule = null;

    #[On('openEditForm')]
    public function openForm(array $payload)
    {
        $this->weekId = $payload['weekId'];
        $this->groupId = $payload['groupId'];

        $this->getUnitsAndMeetings($this->groupId);

        $this->loadSchedule();
        $this->dispatch('open-modal', id: 'week-content-form');
    }

    public function loadSchedule()
    {
        $this->schedule = PlannerWeeklySchedule::where('group_id', $this->groupId)
            ->where('planner_week_id', $this->weekId)
            ->first();

        if ($this->schedule) {
            $this->contentId = $this->schedule->planner_curriculum_contents_id;
            $this->customContent = $this->schedule->custom_content;
            $this->showCustomContent = $this->schedule->show_custom_content;
            $this->units = $this->schedule->units ?? [];
            $this->meetings = $this->schedule->meetings ?? [];
            $this->contentType = $this->schedule->content_type;
            // dd($this->schedule->show_custom_content);
        } else {
            $this->contentId = null;
            $this->customContent = '';
            $this->showCustomContent = false;
            $this->units = [];
            $this->meetings = [];
            $this->contentType = ContentType::DEFAULT;
        }

        $this->form->fill([
            'contentId' => $this->contentId,
            'customContent' => $this->customContent,
            'showCustomContent' => $this->showCustomContent,
            'units' => $this->units,
            'meetings' => $this->meetings,
            'contentType' => $this->contentType,
        ]);
    }

    public function getUnitsAndMeetings($groupId)
    {
        $filteredUnits = [];
        $filteredMeetings = [];

        $activeCurriculaIds = PlannerGroupCurriculum::where('group_id', $groupId)
            ->where('is_active', true)
            ->pluck('planner_curriculum_id');

        if ($activeCurriculaIds->isNotEmpty()) {
            $units = PlannerCurriculumContent::whereIn('planner_curriculum_id', $activeCurriculaIds)
                ->with('units')
                ->get()
                ->pluck('units')
                ->flatten()
                ->unique('id');

            $meetings = PlannerCurriculumContent::whereIn('planner_curriculum_id', $activeCurriculaIds)
                ->with('meetings')
                ->get()
                ->pluck('meetings')
                ->flatten()
                ->unique('id');

            // Map ids to stuff
            foreach ($units as $unit) {
                $filteredUnits[$unit->id] = $unit->name;
            }

            foreach ($meetings as $meeting) {
                $filteredMeetings[$meeting->id] = $meeting->description;
            }
        }

        $this->unitNames = $filteredUnits;
        $this->meetingNames = $filteredMeetings;
    }

    #[Computed]
    public function curriculumContentOptions(): array
    {
        if ($this->groupId) {
            $groupCurricula = PlannerGroupCurriculum::where('group_id', $this->groupId)
                ->where('is_active', true)
                ->pluck('planner_curriculum_id');

            return PlannerCurriculumContent::whereIn('planner_curriculum_id', $groupCurricula)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->id => 'week ' . $item->sort];
                })
                ->toArray();
        }

        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('contentType')
                    ->options(ContentType::class)
                    ->selectablePlaceholder(false)
                    ->default(ContentType::DEFAULT)
                    ->required()
                    ->live(),
                Checkbox::make('showCustomContent')
                    ->live()
                    ->label('Show custom text'),
                Textarea::make('customContent')
                    ->label('Custom text shown in the trainees\' schedule')
                    ->placeholder('Custom Content')
                    ->hidden(fn (Get $get): bool => !$get('showCustomContent')),
                Select::make('units')
                    ->multiple()
                    ->label('Units studied this week')
                    ->hidden(fn (Get $get): bool => !in_array($get('contentType'), [
                        ContentType::DEFAULT,
                        ContentType::UNIT_ONLY,
                    ]))
                    ->options($this->unitNames),
                Select::make('meetings')
                    ->label('Meetings this week')
                    ->multiple()
                    ->hidden(fn (Get $get): bool => !in_array($get('contentType'), [
                        ContentType::DEFAULT,
                        ContentType::MEETING_ONLY,
                    ]))
                    ->options($this->meetingNames),
                // TODO: After reload, the first option should be the current week's content but it's not loaded correctly. It only works after opening another modal and coming back
                Select::make('contentId')
                    ->required()
                    ->selectablePlaceholder(false)
                    ->label('Curriculum content')
                    ->helperText('Only change this if you have customized a group\'s schedule by adding a brush-up week or a deviation from the normal curriculum. This is needed so that the automatic schedule generation knows where it should it continue from.')
                    ->options(fn () => $this->curriculumContentOptions),
            ])
            ->statePath('data');
    }

    public function create()
    {
        $formData = $this->form->getState();
        
        PlannerWeeklySchedule::updateOrCreate(
            [
                'group_id' => $this->groupId,
                'planner_week_id' => $this->weekId,
            ],
            [
                'planner_curriculum_contents_id' => $formData['contentId'] ?? null,
                'custom_content' => $formData['customContent'] ?? null,
                'show_custom_content' => $formData['showCustomContent'] ?? false,
                'units' => $formData['units'] ?? [],
                'meetings' => $formData['meetings'] ?? [],
                'content_type' => $formData['contentType'] ?? null,
            ]
        );

        $this->dispatch('contentUpdated');
        $this->dispatch('close-modal', id: 'week-content-form');
    }

    public function render()
    {
        return view('livewire.planner.week-content-form');
    }
}
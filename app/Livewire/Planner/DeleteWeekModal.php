<?php

namespace App\Livewire\Planner;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;
use App\Models\Planner\PlannerWeeklySchedule;

class DeleteWeekModal extends Component
{   
    public ?int $weekId;
    public ?int $groupId;

    #[On('confirmWeekDelete')]
    public function openDeleteModal(array $payload)
    {
        $this->weekId = $payload['weekId'];
        $this->groupId = $payload['groupId'];

        $this->authorize('delete');
        
        $this->dispatch('open-modal', id: 'confirm-delete-week-info');
    }
    
    public function deleteWeekInfo()
    {
        PlannerWeeklySchedule::where('group_id', $this->groupId)
            ->where('planner_week_id', $this->weekId)
            ->delete();

        Cache::forget("trainee_count_{$this->groupId}");
        $this->resetProperties();
        
        $this->dispatch('contentUpdated');
        $this->dispatch('close-modal', id: 'confirm-delete-week-info');
    }

    public function resetProperties()
    {
        $this->weekId = null;
        $this->groupId = null;
    }

    public function render()
    {
        return view('livewire.planner.delete-week-modal');
    }
}

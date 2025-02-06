<?php

namespace App\Livewire\Dashboard;

use App\Models\Flag\FlagTrainee;
use App\Models\Trainee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FlagTraineeHistory extends Component
{
    public $trainee_flags;

    public function render()
    {
        $trainee = Trainee::select(['id'])
            ->where('user_id', Auth::user()->id)
            ->first();

        $this->trainee_flags = FlagTrainee::where('trainee_id', $trainee->id)
            ->isActive()
            ->whereHas('flag', fn ($query) => $query->where('visible_to_trainee', 1))
            ->with(['flag' => fn ($query) => $query->select(['id', 'name'])])
            ->latest()
            ->get();

        return view('livewire.dashboard.flag-trainee-history');
    }
}

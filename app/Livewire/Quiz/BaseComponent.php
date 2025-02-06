<?php

namespace App\Livewire\Quiz;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Quizzes\Quizzes;
use App\Models\Quizzes\Quiz_versions;

class BaseComponent extends Component {
    use WithPagination; 
    public $quiz = [];
    public $quiz_versions;
    public $search = '';
    public $exceptions = [];

    public function mount() {
        $this->fetch_quizzes();

        $this->quiz_versions = Quiz_versions::with('creator','attempts')
            ->latest('created_at','desc')
            ->get()
            ->map(function($version){
                $version->hidden = true;
                return $version;
            })->toArray();
    }

    public function fetch_quizzes(){
        $this->quiz = Quizzes::with('quiz_versions')
            ->whereHas('quiz_versions', function ($query){
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest('created_at')
            ->get()
            ->map(function ($quiz) {
                $quiz->expanded = false;
                return $quiz;
            })
            ->toArray();
    }

    public function quiz_latest($quiz_id){
        return Quiz_versions::where('quiz_id',$quiz_id)
            ->with('creator','attempts')
            ->latest('created_at','desc')
            ->first();
    }

    public function toggle_versions($quiz_id){
        $updated_version_array = [];
        $updated_quiz_array    = [];
        foreach ($this->quiz_versions as $key => $quiz_version):
            if ($quiz_version['quiz_id'] === $quiz_id):
                $quiz_version['hidden'] = !$quiz_version['hidden'];
            endif;
            $updated_version_array[] = $quiz_version;
        endforeach;

        foreach($this->quiz as $quiz):
            if($quiz['id'] === $quiz_id):
                $quiz['expanded'] = !$quiz['expanded'];
            endif;
            $updated_quiz_array[] = $quiz;
        endforeach;

        $this->quiz_versions = $updated_version_array;
        $this->quiz          = $updated_quiz_array;
    }

    public function rate($percentage){
        if($percentage == 100):
            return "perfect";
        elseif($percentage >= 90 && $percentage <= 99):
            return "outstanding";
        elseif($percentage >= 75 && $percentage <= 89):
            return "good";
        elseif($percentage >= 60 && $percentage <= 74):
            return "satisfactory";
        elseif($percentage >= 50 && $percentage <= 59):
            return "need-improvement";
        elseif($percentage < 50):
            return "poor";
        endif;
    }
}
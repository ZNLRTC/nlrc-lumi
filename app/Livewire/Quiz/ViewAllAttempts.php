<?php

namespace App\Livewire\Quiz;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_attempts;
use App\Models\Quizzes\Quiz_questionnaires;

class ViewAllAttempts extends BaseComponent {
    public $quiz_id;
    public $qv_id;
    public $role;
    public $all_attempts = [];

    public function mount(){
        $this->quiz_id = request()->quiz ? Crypt::decrypt(request()->quiz) : null;
        $this->qv_id   = request()->qv ? Crypt::decrypt(request()->qv) : null;
        $this->role    = Auth::user()->role->name;
        $trainee_id    = (request()->trainee) ? Crypt::decrypt(request()->trainee) : null;
        $quiz          = request()->quiz ? 
                            Quiz_versions::where('quiz_id',$this->quiz_id)->get()->toArray() :
                            Quiz_versions::where('id',$this->qv_id)->get()->toArray();

        if(request()->quiz):
            foreach ($quiz as $version):
                if($this->role == "Trainee" || $trainee_id !== null):
                    $attempts = Quiz_attempts::where([
                        'quiz_version_id' => $version['id'],
                        'trainee_id' => Auth::user()->id
                    ])->with('quiz_version')->get()->toArray();
                else:
                    $attempts = Quiz_attempts::where([
                        'quiz_version_id' => $version['id'],
                    ])->with('quiz_version','trainee')->get()->toArray();
                endif;
                $this->all_attempts = array_merge($this->all_attempts, $attempts);
            endforeach;
        elseif(request()->qv):
            if($this->role == "Trainee" || $trainee_id !== null):
                $this->all_attempts = Quiz_attempts::where([
                    'quiz_version_id' => $this->qv_id,
                    'trainee_id' => Auth::user()->id
                ])->with('quiz_version')->get()->toArray();
            else:
                $this->all_attempts = Quiz_attempts::where([
                    'quiz_version_id' => $this->qv_id,
                ])->with('quiz_version','trainee')->get()->toArray();
            endif;
        endif;

        usort($this->all_attempts, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
    }

    public function count_questionnaires($qv_id){
        return count(Quiz_questionnaires::where('quiz_version_id',$qv_id)->get());
    }
    
    public function render(){
        return view('livewire.quiz.view-all-attempts');
    }
}
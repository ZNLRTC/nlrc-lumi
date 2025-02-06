<?php

namespace App\Livewire\Quiz;

use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_attempts;
use App\Models\Quizzes\Quiz_questionnaires;

class StudentDashboard extends BaseComponent {

    public function get_versions_list($quiz_id){
        return Quiz_versions::where('quiz_id',$quiz_id)->get()->toArray();
    }

    public function get_trainee_attempts($qv_id, $trainee_id){
        return Quiz_attempts::where(['quiz_version_id' => $qv_id, 'trainee_id' => $trainee_id])->get()->toArray();
    }

    public function questionnaires($qv_id){
        return  Quiz_questionnaires::get_all_questionnaires_where($qv_id)->toArray();
    }

    public function render() {
        return view('livewire.quiz.student-dashboard');
    }
}
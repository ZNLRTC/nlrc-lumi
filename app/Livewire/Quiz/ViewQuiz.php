<?php

namespace App\Livewire\Quiz;

use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_choice_option_versions;
use App\Models\Quizzes\Quiz_questionnaires;

class ViewQuiz extends Component {
    public $qv_id;
    public $quiz           = [];
    public $questionnaires = [];
    public $options        = [];

    public function mount(){
        $this->qv_id          = Crypt::decrypt(request()->quiz);
        $this->quiz           = Quiz_versions::where('id',$this->qv_id )->get()->toArray();
        $this->questionnaires = Quiz_questionnaires::get_all_questionnaires_where($this->qv_id)->toArray();
        $this->options        = Quiz_choice_option_versions::get_all_version_options($this->qv_id)->toArray();
    }

    public function filter_array($qqv_id){
        return array_filter($this->options, function($option) use ($qqv_id) {
            return $option['quiz_questionnaire_version_id'] == $qqv_id;
        });
    }
    
    public function render(){
        return view('livewire.quiz.view-quiz');
    }
}
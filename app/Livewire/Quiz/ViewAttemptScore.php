<?php

namespace App\Livewire\Quiz;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Quizzes\Quiz_answers;
use App\Models\Quizzes\Quiz_choice_options;
use App\Models\Quizzes\Quiz_attempts;
use App\Models\Quizzes\Quiz_attempt_answers;
use App\Models\Quizzes\Quiz_questionnaires;
use App\Models\Quizzes\Quiz_questionnaire_versions;
use App\Models\Quizzes\Quiz_choice_option_versions;


class ViewAttemptScore extends Component {
    public $attempt_id;
    public $attempt        = [];
    public $answers        = [];
    public $questionnaires = [];
    public $options        = [];
    public $commentaries   = [];
    public $wrong_answers  = [];

    public function mount(){
        $this->attempt_id     = Crypt::decrypt(request()->attempt);
        $this->attempt        = Quiz_attempts::where('id',$this->attempt_id)->with('quiz_version')->first()->toArray();
        $this->answers        = Quiz_attempt_answers::answers_array($this->attempt_id);
        $this->questionnaires = Quiz_questionnaires::get_all_questionnaires_where($this->attempt['quiz_version_id'])->toArray();
        $this->options        = Quiz_choice_option_versions::get_attempt_options($this->attempt['quiz_version_id'])->toArray();
        
        $score = ($this->attempt['score']/count($this->questionnaires))*100;
        if($score == 100):
            $this->commentaries = ["perfect","Excellent performance! You've achieved a perfect score, demonstrating complete mastery of the material."];
        elseif($score >= 90 && $score <= 99):
            $this->commentaries = ["outstanding","Great job! You've shown a strong understanding with only a few minor mistakes."];
        elseif($score >= 75 && $score <= 89):
            $this->commentaries = ["good","Well done! You've grasped most of the concepts, but there's room for improvement on a few topics."];
        elseif($score >= 60 && $score <= 74):
            $this->commentaries = ["satisfactory","Decent effort, but there are several areas that could use more attention and review."];
        elseif($score >= 50 && $score <= 59):
            $this->commentaries = ["need-improvement","You have a basic understanding, but you'll need to work on key concepts to improve your score."];
        elseif($score < 50):
            $this->commentaries = ["poor","There's significant room for improvement. Focus on reviewing the material and practicing more to boost your understanding."];
        endif;

        foreach($this->answers as $key => $answer):
            $question = $this->qqv($key);            
            if($question['question_type'] == "boolean"):
                $compare = $this->q_options($key);
                if($compare['latest_version']['option'] !== $answer['answer'][0]):
                    $this->wrong_answers[] = "question-".$key;
                endif;
            elseif($question['question_type'] == "multiple-choice"):
                $compare = $this->q_answers($key);
                if($compare['quiz_choice_option_version_id'] != $answer['answer'][0]):
                    $this->wrong_answers[] = "question-".$key;
                endif;
            elseif($question['question_type'] == "check-box"):
                $count_options = $this->count_options($key);
                $check         = 0;
                foreach($answer['answer'] as $option):
                    $compare = $this->q_answers($key,$option);
                    $check += $compare ? 1 : 0;
                endforeach;
                if($count_options !== $check):
                    $this->wrong_answers[] = "question-".$key;
                endif;
            elseif($question['question_type'] == "written"):
                $compare       = $this->q_options($key,true);
                $count         = 0;
                foreach($compare as $regex):
                    if(strtolower(str_replace(' ', '', $regex['latest_version']['option'])) == strtolower(str_replace(' ', '', $answer['answer'][0]))):
                        $count++;
                    endif;
                endforeach;

                if($count == 0):
                    $this->wrong_answers[] = "question-".$key;
                endif;
            endif;
        endforeach;
    }

    public function qqv($index){
        return Quiz_questionnaire_versions::where('id',$index)->first()->toArray();
    }

    public function q_options($index, $multiple = null){
        if($multiple):
            return Quiz_choice_options::where('quiz_questionnaire_version_id',$index)
            ->with('latest_version')->get()->toArray();
        else:
            return Quiz_choice_options::where('quiz_questionnaire_version_id',$index)
            ->with('latest_version')->first()->toArray();
        endif;
    }

    public function count_options($index){
        return count(Quiz_answers::where('quiz_questionnaire_version_id',$index)->get());
    }

    public function q_answers($index, $key = null){
        if($key):
            return  Quiz_answers::where([
                'quiz_questionnaire_version_id' => $index,
                'quiz_choice_option_version_id' => $key
            ])->first();
        else:
            return Quiz_answers::where('quiz_questionnaire_version_id',$index)
        ->first()->toArray();
        endif;
    }

    public function render(){
        // Try to add a valdiation here, that if the attempt_id do not exist redirect to 404
        return view('livewire.quiz.view-attempt-score');
    }
}   
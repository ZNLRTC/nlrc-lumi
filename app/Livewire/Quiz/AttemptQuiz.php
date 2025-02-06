<?php

namespace App\Livewire\Quiz;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_answers;
use App\Models\Quizzes\Quiz_choice_options;
use App\Models\Quizzes\Quiz_choice_option_versions;
use App\Models\Quizzes\Quiz_questionnaires;
use App\Models\Quizzes\Quiz_questionnaire_versions;

use Exception as BaseException;
use Illuminate\Database\QueryException;

class AttemptQuiz extends Component {
    public $qv_id;
    public $attempt_id;
    public $disabled       = false;
    public $quiz           = [];
    public $questionnaires = [];
    public $options        = [];
    public $error          = [];
    public $answers        = [];

    public function mount(){
        $this->qv_id          = Crypt::decrypt(request()->quiz);
        $this->quiz           = Quiz_versions::where('id',$this->qv_id )->get()->toArray();
        $this->questionnaires = Quiz_questionnaires::get_all_questionnaires_where($this->qv_id)->toArray();
        $this->options        = Quiz_choice_option_versions::get_attempt_options($this->qv_id)->toArray();
    }

    public function filter_array($qqv_id){
        return array_filter($this->options, function($option) use ($qqv_id) {
            return $option['quiz_questionnaire_version_id'] == $qqv_id;
        });
    }

    public function update_answer($qqv_id,$option_id,$q_type=null){
        if($q_type == 'radio'):
            $this->answers[$qqv_id] = [];
            $this->answers[$qqv_id][$option_id] = true;
        else:
            if(in_array($option_id, $this->answers[$qqv_id] ?? [])):
                $this->answers[$qqv_id] = array_diff($this->answers[$qqv_id] ?? [], [$option_id]);
            else:
                $this->answers[$qqv_id][] = $option_id;
            endif;
        endif;
    }

    public function test(){
        return Quiz_choice_options::where('quiz_questionnaire_version_id',15)
        ->with('latest_version')->get()->toArray();
    }

    public function submit(){
        $this->error = [];
        $count_error = 0;

        foreach ($this->questionnaires as $index => $question):
            if (!array_key_exists($question['latest_version']['id'], $this->answers)):
                $this->error[] = "question-".$index;
                $count_error++;
            else:
                if($question['latest_version']['question_type'] == "written" && trim($this->answers[$question['latest_version']['id']]['value']) == ""):
                    $this->error[] = "question-".$index;
                    $count_error++;
                else:
                    $this->error[] = null;
                endif;
            endif;
        endforeach;

        if($count_error > 0):
            session()->flash('error', "Please answer all the questions");
        else:
            try{
                $this->disabled = true;
                DB::beginTransaction();
                //Insert Quiz_attempt
                $quiz_attempt_data = [
                    "trainee_id" => Auth::user()->id,
                    "quiz_version_id"  => $this->qv_id,
                    "score" => 0 //default value for now
                ];

                $this->attempt_id = DB::table('quiz_attempts')->insertGetId($quiz_attempt_data);
                $score            = 0;
                foreach($this->answers as $index => $answer):
                    $q_type      = Quiz_questionnaire_versions::where('id',$index)->first()->toArray();
                    $check_count = ($q_type['question_type'] == "check-box") ? count(Quiz_answers::where('quiz_questionnaire_version_id',$index)->get()) : 0;
                    $check       = 0;
                    foreach($this->answers[$index] as $key => $option):
                        $quiz_attempt_answer = [
                            "quiz_attempt_id"               => $this->attempt_id,
                            "quiz_questionnaire_version_id" => $index,
                            "answer"                        => ($q_type['question_type'] == "boolean" || $q_type['question_type'] == "written") ? $option : $key
                        ];
                        DB::table('quiz_attempt_answers')->insert($quiz_attempt_answer);
                        
                        //Compute score
                        if($q_type['question_type'] == "boolean"):
                            $compare = Quiz_choice_options::where('quiz_questionnaire_version_id',$index)
                                ->with('latest_version')->first()->toArray();
                            $score += ($compare['latest_version']['option'] == $option) ? 1 : 0;
                        elseif($q_type['question_type'] == "multiple-choice"):
                            $compare = Quiz_answers::where('quiz_questionnaire_version_id',$index)
                                ->first()->toArray();
                            $score += ($compare['quiz_choice_option_version_id'] == $key) ? 1 : 0;
                        elseif($q_type['question_type'] == "check-box"):
                            $compare = Quiz_answers::where([
                                'quiz_questionnaire_version_id' => $index,
                                'quiz_choice_option_version_id' => $key
                                ])->first();
                            $check += $compare ? 1 : 0;
                        elseif($q_type['question_type'] == "written"):
                            $compare = Quiz_choice_options::where('quiz_questionnaire_version_id',$index)
                                ->with('latest_version')->get()->toArray();
                            foreach($compare as $regex):
                                if(strtolower(str_replace(' ', '', $option)) == strtolower(str_replace(' ', '', $regex['latest_version']['option']))):
                                    $score++;
                                    break;
                                endif;
                            endforeach;
                        endif;
                    endforeach;
                    $score += (($q_type['question_type'] == "check-box") && ($check_count == $check)) ? 1 : 0;
                endforeach;
                //Update score on database
                DB::table('quiz_attempts')->where("id",$this->attempt_id)->update(['score' => $score]);

                $this->disabled = true;
                session()->flash('success', "Quiz Attempt submitted successfully. Redirecting to score board....");
                DB::commit();
            }catch(QueryException $e){
                DB::rollBack();
                $this->disabled = false;
                $this->error = [
                    'status'  => 'error',
                    'message' => 'An unexpected query error occurred, please try again later.',
                    'error'   => $e->getMessage()
                ];
            }catch(BaseException $e){
                DB::rollBack();
                $this->disabled = false;
                $this->error = [
                    'status'  => 'error',
                    'message' => 'An unexpected base error occurred, please try again later.',
                    'error'   => $e->getMessage()
                ];
            }
        endif;
    }

    public function render(){
        return view('livewire.quiz.attempt-quiz');
    }
}   
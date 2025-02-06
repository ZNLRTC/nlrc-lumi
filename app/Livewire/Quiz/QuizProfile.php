<?php

namespace App\Livewire\Quiz;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_choice_option_versions;
use App\Models\Quizzes\Quiz_choice_options;
use App\Models\Quizzes\Quiz_questionnaires;
use App\Models\Quizzes\Quiz_questionnaire_versions;
use App\Models\Quizzes\Quiz_answers;

use Exception as BaseException;
use Illuminate\Database\QueryException;
use PgSql\Lob;

class QuizProfile extends Component {
    public $qv_id;
    public $quiz           = [];
    public $questionnaires = [];
    public $options        = [];
    public $error          = [];
    public $exceptions     = [];

    public function mount(){
        //request()->quiz is already being validation on Controller
        $this->qv_id          = Crypt::decrypt(request()->quiz);
        $this->quiz           = Quiz_versions::where('id',$this->qv_id )->get()->toArray();
        $this->questionnaires = Quiz_questionnaires::get_all_questionnaires_where($this->qv_id)->toArray();
        $this->options        = Quiz_choice_option_versions::get_all_options_where($this->qv_id)->toArray();
    }

    public function filter_array($qqv_id){
        return array_filter($this->options, function($option) use ($qqv_id) {
            return $option['quiz_questionnaire_version_id'] == $qqv_id;
        });
    }

    public function update_boolean($index, $value){
        $this->options[$index]['option'] = $value;
    }

    public function update_quiz_info($field, $value){
        $this->quiz[0][$field] = $value;
        $this->render();
    }
    
    public function update_content($index, $value, $field, $qqv_id = null){
        if($field):
            $this->questionnaires[$index]['latest_version'][$field] = $value;
            $count = count($this->options);

            if($value == "multiple-choice" || $value == "check-box" || $value == "written"):
                $this->questionnaires[$index]['latest_version']['explanation'] = '';
                //delete all options under it by default
                $this->remove_option(null, $qqv_id);
                //then push new values
                for($i = 0; $i < 2; $i++):
                    $this->add_option($qqv_id,$count + $i, null, null);
                endfor;
            elseif($value == "boolean"):
                $this->add_option($qqv_id, $count, null, null);
            endif;
        else:
            $this->options[$index]['option'] = $value;
        endif;
    }
 
    public function add_question($index, $count, $question = null, $question_type = null, $explanation = null){
        array_splice($this->questionnaires, $index, 0, [[
            "id"             => null,
            "latest_version" => [
                'id'                    => 'default-null-'.uniqid($count),
                'question'              => $question,
                'question_type'         => $question_type,
                'explanation'           => $explanation,
            ]
        ]]);
        $this->render();
    }

    public function remove_question($index){
        $qqv_id = $this->questionnaires[$index]['latest_version']['id'];
        $this->remove_option(null, $qqv_id);

        unset($this->questionnaires[$index]);
        //Reset indexes
        $this->questionnaires = array_values($this->questionnaires);
        $this->render();
    }

    public function add_option($qqv_id, $key, $value = null, $checked){
        array_splice($this->options, $key+1, 0, [[
            "quiz_version_id"               => $this->qv_id,
            "quiz_questionnaire_version_id" => $qqv_id,
            "id"                            => 'default-option-null-'.uniqid($key),
            "option"                        => $value,
            "checked"                       => $checked
        ]]);
        $this->render();
    }

    public function remove_option($index = null, $qqv_id = null){
        if($qqv_id):
            foreach($this->options as $key => $option):
                if ($option['quiz_questionnaire_version_id'] == $qqv_id):
                    unset($this->options[$key]);
                endif;
            endforeach;
        else:
            unset($this->options[$index]);
        endif;
        //Reset indexes
        $this->options = array_values($this->options);
        $this->render();
    }

    public function add_answer($qqv_id, $option_id, $field){
        if($field == "radio"):
            foreach($this->options as $key => $option):
                if ($option['quiz_questionnaire_version_id'] == $qqv_id):
                    if($option['id'] == $option_id):
                        $this->options[$key]['checked'] = 1;
                    else:
                        $this->options[$key]['checked'] = null;
                    endif;
                endif;
            endforeach;
        else:
            foreach($this->options as $key => $option):
                if ($option['quiz_questionnaire_version_id'] == $qqv_id):
                    //Bat baliktad logic here, but attains desired result
                    if($option['id'] == $option_id && !empty($option['checked'])):
                        $this->options[$key]['checked'] = 1;
                    elseif($option['id'] == $option_id && empty($option['checked'])):
                        $this->options[$key]['checked'] = null;
                    endif;
                endif;
            endforeach;
        endif;
    }

    public function insert_new_quiz_questionnaire_version($question, $question_type, $explanation){
        return DB::table('quiz_questionnaire_versions')->insertGetId([
            "question"      => $question,
            "question_type" => $question_type,
            "explanation"   => $explanation
        ]);
    }

    public function insert_new_quiz_questionnaire($index, $new_quiz_version_id,$new_qqv_id){
        //insert new quiz_questionnaire
        $new_qq_id = DB::table('quiz_questionnaires')->insertGetId([
            "sort_number"     => $index+1,
            "quiz_version_id" => $new_quiz_version_id,
            "version_id"      => $new_qqv_id
        ]);
        
        //Then update the the newly added versions quiz_questionnaire_id
        DB::table('quiz_questionnaire_versions')
            ->where('id',$new_qqv_id)
            ->update(['quiz_questionnaire_id' => $new_qq_id]);

        return $new_qq_id;
    }

    public function insert_new_option($option, $qqv_id){
        $option_id = DB::table('quiz_choice_option_versions')->insertGetId(['option' => $option]);

        $qco_id = DB::table('quiz_choice_options')->insertGetId([
            'quiz_questionnaire_version_id' => $qqv_id,
            'version_id' => $option_id,
        ]);

        DB::table('quiz_choice_option_versions')
            ->where('id', $option_id)
            ->update(['quiz_choice_option_id' => $qco_id]);

        return $option_id;
    }

    public function submit(){
        //REVALIDATE REQUIRED OPTIONS LATER
        //DO MAKE SURE THAT THE ONLY VALUES ON ALL DROPDOWNS ARE THE 4
        //IF NO ATTEMPTS ARE MADE, JUST UPDATE NO NEED TO CREATE NEW VERSION
        $this->error      = [];
        $this->exceptions = [];
        $count_error      = 0;
        $create_new       = false;
        $test_message     = "Basta wala";
        foreach($this->questionnaires as $index => $question):
            $qqv_id = $question['latest_version']['id'];
            $option = [];
            if($question['latest_version']['question_type'] !== 'written'):
                $filter = ($question['latest_version']['question_type'] == 'boolean')? 'option' : 'checked';
                $option =  array_filter($this->options, function($option) use ($qqv_id, $filter) {
                    return $option['quiz_questionnaire_version_id'] == $qqv_id && $option[$filter] !== null;
                });
                
                if(count($option) > 0):
                    array_push($this->error, null );
                else:
                    array_push($this->error, 'question-'.$index );
                    $count_error++;
                endif;
            else:
                array_push($this->error, null);
            endif;
        endforeach;
        
        if($count_error == 0):
            //[SECTION] Fetch the original contents
            $original_questions = Quiz_questionnaires::get_all_questionnaires_where($this->qv_id)->toArray();
            $original_options   = Quiz_choice_option_versions::get_all_options_where($this->qv_id)->toArray();
            
            if(count($original_questions) == count($this->questionnaires)):
                foreach($this->questionnaires as $key => $modified):
                    if(
                        !isset($original_questions[$key]) || (
                        $modified['latest_version']['question']      !== $original_questions[$key]['latest_version']['question'] ||
                        $modified['latest_version']['question_type'] !== $original_questions[$key]['latest_version']['question_type'] ||
                        $modified['latest_version']['explanation']   !== $original_questions[$key]['latest_version']['explanation']
                    )):
                        $create_new = true;
                        break;
                    endif;
                endforeach;
            elseif(count($original_questions) !== count($this->questionnaires)): 
                $create_new = true;
            endif;

            //if any of its options are changed create a new version
            if($create_new == false && (count($original_options) == count($this->options))):
                foreach($this->options as $key => $modified):
                    $q_type = Quiz_questionnaire_versions::where(['id' => $modified['quiz_questionnaire_version_id']])->first()['question_type'];
                    if($q_type == "multiple-choice" || $q_type == "check-box"):
                        if( !isset($original_options[$key]) || (
                            $modified['option']  !== $original_options[$key]['option'] ||
                            $modified['checked'] !== $original_options[$key]['checked']
                        )):
                            $create_new = true;
                            break;
                        endif;
                    else:
                        if( !isset($original_options[$key]) || $modified['option']  !== $original_options[$key]['option'] ):
                            $create_new = true;
                            break;
                        endif;
                    endif;
                endforeach;
            elseif($create_new == false && (count($original_options) !== count($this->options))): 
                $create_new = true;
            endif;
            
            try{
                $quiz_version_data = [
                    'quiz_id'                 => $this->quiz[0]['quiz_id'],
                    'title'                   => strip_tags($this->quiz[0]['title']),
                    'title-translation'       => strip_tags($this->quiz[0]['title-translation']),
                    'description'             => strip_tags($this->quiz[0]['description']),
                    'description-translation' => strip_tags($this->quiz[0]['description-translation']),
                    'version_number'          => $this->quiz[0]['version_number']+1,
                    'created_by'              => ($create_new == false) ? $this->quiz[0]['created_by'] :  Auth::id(),
                    'updated_by'              => ($create_new == false) ? Auth::id() : null
                ];

                DB::beginTransaction();
                if($create_new == false):
                    DB::table('quiz_versions')
                        ->where('id',$this->quiz[0]['id'])
                        ->update($quiz_version_data);
                    
                    DB::table('quizzes')
                        ->where('id',$this->quiz[0]['quiz_id'])
                        ->update(['current_title' => $quiz_version_data['title']]);
                else:
                    //Create a new quiz_version
                    $new_quiz_version_id = DB::table('quiz_versions')->insertGetId($quiz_version_data);
                    
                    //Update the main quiz info
                    DB::table('quizzes')
                        ->where('id',$quiz_version_data['quiz_id'])
                        ->update([
                            "current_title" => $quiz_version_data['title'],
                            "version_id"    => $new_quiz_version_id
                        ]);

                    //create a new quiz_questionnaire pattern
                    foreach($this->questionnaires as $index => $question):
                        $question_query     = $question['latest_version']['question'];
                        $question_type      = $question['latest_version']['question_type'];
                        $explanation        = $question['latest_version']['explanation'];
                        $question_options   = Quiz_choice_options::where(['quiz_questionnaire_version_id' => $question['latest_version']['id']])->get();
                        $new_options        = array_values($this->filter_array($question['latest_version']['id']));
                        $qqv_id             = $question['latest_version']['id'];
                        $exist              = Quiz_questionnaire_versions::where('id', $qqv_id)->first();
                        $create_new_version = !$exist || ( $exist &&
                            ($question_query !== $exist['question'] ||
                            $question_type  !== $exist['question_type'] ||
                            $explanation    !== $exist['explanation'])
                        );

                        //If if change is detected or do not exist create a new version
                        if ($create_new_version || !$question_options || 
                            ($question_options && count($question_options) !== count($new_options))
                        ):
                            //create a new quiz_questionnaire_version and replace the $qqv_id default
                            $qqv_id = $this->insert_new_quiz_questionnaire_version(
                                $question_query, $question_type,  $explanation
                            );
                        elseif($question_options && count($question_options) == count($new_options)):
                            foreach($new_options as $key => $option):
                                $option_exist      = Quiz_choice_option_versions::where(['id' => $option['id']])->first();
                                $create_new_option = !$option_exist || ( $option_exist && $option['option'] !== $option_exist['option'] );
                                //If its any of its options are changed or do not exist, do create a new questionnaire version
                                if($create_new_option):
                                    $qqv_id = $this->insert_new_quiz_questionnaire_version(
                                        $question_query, $question_type,  $explanation
                                    );
                                    break;
                                endif;
                            endforeach;
                        endif;

                        $this->insert_new_quiz_questionnaire($index, $new_quiz_version_id, $qqv_id);
                        //With updated $qqv_id
                        $question_options = Quiz_choice_options::where(['quiz_questionnaire_version_id' => $qqv_id])->get();
                        if($question_type == "boolean"):
                            if($question_options):
                                foreach($new_options as $key => $option):
                                    $option_exist      = Quiz_choice_option_versions::where(['id' => $option['id']])->first();
                                    $create_new_option = !$option_exist || count($question_options) !== count($new_options) || ( $option_exist && $option_exist['option'] !== $option['option']);
                                    if($create_new_option):
                                        //create new
                                        $option_id = $this->insert_new_option($option['option'], $qqv_id);
                                        break;
                                    endif;
                                endforeach; 
                            else:
                                //create news
                                $option_id = $this->insert_new_option($new_options[0]['option'], $qqv_id);
                            endif;
                        else:
                            // for multiple choice and checkbox do check its answers
                            // for regex, just the options
                            if($question_options):
                                foreach($new_options as $key => $option):
                                    $option_exist      = Quiz_choice_option_versions::where(['id' => $option['id']])->first();
                                    $create_new_option = !$option_exist || count($question_options) !== count($new_options) || ( $option_exist && $option['option'] !== $option_exist['option'] );
                                    $option_id         = $option['id'];
                                    if($create_new_option):
                                        //create new
                                        $option_id = $this->insert_new_option($option['option'], $qqv_id);
                                    endif;

                                    if($option['checked'] !== null):
                                        //check if existing on answers
                                        $answer_exist = Quiz_answers::where([
                                            'quiz_questionnaire_version_id' => $qqv_id,
                                            'quiz_choice_option_version_id'  => $option_id
                                        ])->first();
                                        
                                        if(!$answer_exist):
                                            //create new answer
                                            Quiz_answers::insert([
                                                'quiz_questionnaire_version_id' => $qqv_id,
                                                'quiz_choice_option_version_id' => $option_id
                                            ]);
                                        endif;
                                    endif;
                                endforeach; 
                            else:
                                foreach($new_options as $key => $option):
                                    $option_id = $this->insert_new_option($option['option'], $qqv_id);
                                    //answers
                                    if($option['checked'] !== null):
                                        //create new answer
                                        Quiz_answers::insert([
                                            'quiz_questionnaire_version_id' => $qqv_id,
                                            'quiz_choice_option_version_id' => $option_id
                                        ]);
                                    endif;
                                endforeach;
                            endif;
                        endif;
                    endforeach;
                endif;
                DB::commit();
            }catch (QueryException $e){
                DB::rollBack();
                // $backtrace = $e->getTrace();
                // $lineNumber = null;
                // foreach ($backtrace as $entry):
                //     if (strpos($entry['file'], 'C:\xampp\htdocs\nlrc-lumi\app\Livewire\Quiz\QuizProfile.php') !== false):
                //         $lineNumber = $entry['line'];
                //         break;
                //     endif;
                // endforeach;
                $this->exceptions = [
                    'status'  => 'error',
                    'message' => 'An unexpected query error occurred, please try again later.',
                    'error'   => $e->getMessage()
                    //'error'   => $e->getMessage()." at  line:".$lineNumber
                ];
            }catch(BaseException $e){
                DB::rollBack();
                //$backtrace = $e->getTrace();
                // $lineNumber = null;
                // foreach ($backtrace as $entry):
                //     if (strpos($entry['file'], 'C:\xampp\htdocs\nlrc-lumi\app\Livewire\Quiz\QuizProfile.php') !== false):
                //         $lineNumber = $entry['line'];
                //         break;
                //     endif;
                // endforeach;

                $this->exceptions = [
                    'status'  => 'error',
                    'message' => 'An unexpected base error occurred, please try again later.',
                    'error'   => $e->getMessage()
                    //'error'   => $e->getMessage()." at  line:".$lineNumber
                ];
            }
        endif;

        if($count_error == 0 && count($this->exceptions) == 0):
            session()->flash('success', 'success');
        elseif($count_error):
            session()->flash('error', 'Do fill-in the required filed above');
        else:
            session()->flash('error', $this->exceptions['message']);
        endif;
    }

    public function render(){
        return view('livewire.quiz.quiz-profile');
    }
}
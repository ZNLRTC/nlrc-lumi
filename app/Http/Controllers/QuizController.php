<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Quizzes\Quizzes;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_attempts;
use App\Models\Courses\Topic;
use Illuminate\Validation\ValidationException;

use Exception as BaseException;
use Illuminate\Database\QueryException;
//Below is for private functions
use ReflectionMethod;

class QuizController extends Controller
{
    public function callFunction($function) {
        if(Auth::check()):
            if (method_exists($this, $function)):
                $reflection = new ReflectionMethod($this, $function);
                return $reflection->isPublic()? $this->{$function}(request()): abort(403, 'Unauthorized');
            else:
                abort(404);
            endif;
        else:
            return redirect("/");
        endif;
    }

    private function filter_keys($array, $compare) {
        $filtered = [];
        $count = 0;
        foreach ($array as $key => $value) {
            if (strpos($key, $compare) === 0) {
                $filtered[$count] = $value;
                $count++;
            }
        }
        return $filtered;
    }

    public function index(){
        if(Auth::user()->role->name == "Trainee"):
            return view('quizzes/quiz_dashboard_student');
        else:
            return view('quizzes/quiz_dashboard');
        endif;
    }

    public function update(){
        if(Auth::user()->role->name == "Trainee"){ abort(404);  }
        if(request()->quiz):
            try {
                $qv_id = Crypt::decrypt(request()->quiz);
                if(Quizzes::where('id',$qv_id)):
                    return view('quizzes/update_quiz');
                else:
                    abort(404);
                endif;
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Quiz not found.');
            }
        else:
            abort(404);
        endif;
    }

    public function view(){
        if(Auth::user()->role->name == "Trainee"){ abort(404);  }
        if(request()->quiz):
            try {
                $qv_id = Crypt::decrypt(request()->quiz);
                if(Quizzes::where('id',$qv_id)):
                    return view('quizzes/view_quiz');
                else:
                    abort(404);
                endif;
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Quiz not found.');
            }
        else:
            abort(404);
        endif;
    }

    public function attempt(){
        if(request()->quiz):
            try {
                $qv_id = Crypt::decrypt(request()->quiz);
                $quiz = Quiz_versions::where('id',$qv_id)->with('parent_quiz')->first();
                if($quiz && $quiz['parent_quiz']['archive'] == false):
                    return view('quizzes/attempt_quiz');
                else:
                    abort(404);
                endif;
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Quiz not found.');
            }
        else:
            abort(404);
        endif;
    }

    public function score(){
        if(request()->attempt):
            try {
                $attempt_id = Crypt::decrypt(request()->attempt);
                $attempt    = Quiz_attempts::where('id',$attempt_id)->first();
                $role       = Auth::user()->role->name;
                if( $attempt &&
                    (
                        $role !== "Trainee" ||
                        ($role == "Trainee" && Auth::user()->id == $attempt->trainee_id)
                    )
                ):
                    return view('quizzes/view_attempt_score');
                else:
                    abort(404);
                endif;
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Quiz not found.');
            }
        else:
            abort(404);
        endif;
    }

    public function view_all_attempts(){
        $role     = Auth::user()->role->name;
        $attempts = 0;
        if(request()->quiz):
            try {
                $quiz_id  = Crypt::decrypt(request()->quiz);
                $quiz     = Quiz_versions::where('quiz_id',$quiz_id)->get()->toArray();

                if($role == "Trainee"):
                    foreach($quiz as $versions):
                        $q_attempts = Quiz_attempts::where(['quiz_version_id' => $versions['id'], 'trainee_id' => Auth::user()->id])->get();
                        $attempts  += ($q_attempts) ? 1 : 0;
                    endforeach;
                endif;

                if( $quiz &&
                    (
                        $role !== "Trainee" ||
                        ($role == "Trainee" && $attempts !== 0)
                    )
                ):
                    return view('quizzes/view_all_attempts');
                else:
                    abort(404);
                endif;
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Quiz not found.');
            }
        elseif(request()->qv):
            $qv_id        = Crypt::decrypt(request()->qv);
            $quiz_version = Quiz_versions::where('id',$qv_id)->get()->toArray();

            if($quiz_version):
                if($role == "Trainee"):
                    $q_attempts = Quiz_attempts::where(['quiz_version_id' => $qv_id, 'trainee_id' => Auth::user()->id])->get();
                    $attempts  += ($q_attempts) ? 1 : 0;
                endif;

                if($role !== "Trainee" || ($role == "Trainee" && $attempts !== 0)):
                    return view('quizzes/view_all_attempts');
                else:
                    abort(404);
                endif;
            else:
                abort(404);
            endif;
        else:
            abort(404);
        endif;
    }

    public function add_quiz(){
        if(Auth::user()->role->name == "Trainee"){ abort(404);  }
        if(request()->topic){
            try {
                $topic = Topic::find(Crypt::decrypt(request()->topic));
                $back = Crypt::decrypt(request()->back);

                if(
                    !$topic ||
                    (request()->topic && !request()->back) ||
                    (!request()->topic && request()->back)
                ) {
                    abort(404);
                }
            } catch (DecryptException $e) {
                abort(404);
            }
        }
        return view('quizzes/add_quiz');
    }

    public function add_section(){
        if(request()->header('X-My-Custom-Header') !== 'fetch-request'){ abort(404); }
        $response = [
            'content' => '
                <div class="form-group question-form" wire:ignore>
                    <div class="row">
                        <div class="col-md-7">
                            <textarea placeholder="Question" rows=1 class="question" required></textarea>
                        </div>
                        <div class="col-md-5">
                            <select class="form-select question-type" title="Select question type" required>
                                <option value="">Select question type</option>
                                <option value="multiple-choice">Multiple choice</option>
                                <option value="check-box">Check box</option>
                                <option value="boolean">True or false</option>
                                <option value="written">Written</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-action">
                            <div class="remove-one">
                                <!--button type="button" class="duplicate-question" title="Duplicate question">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <path d="M208 0L332.1 0c12.7 0 24.9 5.1 33.9 14.1l67.9 67.9c9 9 14.1 21.2 14.1 33.9L448 336c0 26.5-21.5 48-48 48l-192 0c-26.5 0-48-21.5-48-48l0-288c0-26.5 21.5-48 48-48zM48 128l80 0 0 64-64 0 0 256 192 0 0-32 64 0 0 48c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 176c0-26.5 21.5-48 48-48z"/>
                                    </svg>
                                </button-->
                                <button type="button" class="delete-question" title="Delete question">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                    </svg>
                                </button>
                                <span> | </span>
                            </div>
                            <button type="button" class="add-question" title="Add question">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            ',
            'action' => '
                <div class="remove-one">
                    <!--button type="button" class="duplicate-question" title="Duplicate question">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M208 0L332.1 0c12.7 0 24.9 5.1 33.9 14.1l67.9 67.9c9 9 14.1 21.2 14.1 33.9L448 336c0 26.5-21.5 48-48 48l-192 0c-26.5 0-48-21.5-48-48l0-288c0-26.5 21.5-48 48-48zM48 128l80 0 0 64-64 0 0 256 192 0 0-32 64 0 0 48c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 176c0-26.5 21.5-48 48-48z"/>
                        </svg>
                    </button-->
                    <button type="button" class="delete-question" title="Delete question">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                        </svg>
                    </button>
                    <span> | </span>
                </div>
            ',
        ];

        return response()->json($response);
    }

    public function question_type(){
        if(request()->header('X-My-Custom-Header') !== 'fetch-request'){ abort(404); }
        $q_type = request()->input("qtype");

        switch($q_type){
            case "multiple-choice":
                $response = [
                    'answer_div' => '
                         <div class="col-md-10 multiple-choice-div" answer-div>
                            <i class="note">Forget not to tick the right option below</i>
                            <div class="option-con">
                                <input type="radio" disabled>
                                <textarea class="option" rows=1 placeholder="Option" required></textarea>
                                <div class="option-actions">
                                    <button type="button" class="add-option" title="Add Option">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="option-con">
                                <input type="radio" disabled>
                                <textarea class="option" rows=1 placeholder="Option" required></textarea>
                                <div class="option-actions">
                                    <button type="button" class="add-option" title="Add Option">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <textarea class="explanation" rows=1 placeholder="Add an explanation (optional)"></textarea>
                        </div>
                    '
                ];
                break;
            case "check-box":
                $response = [
                    'answer_div' => '
                         <div class="col-md-10 checkbox-div" answer-div>
                            <i class="note">Forget not to tick the right option/s below</i>
                            <div class="option-con">
                                <input type="checkbox" disabled>
                                <textarea class="option" rows=1 placeholder="Option" required></textarea>
                                <div class="option-actions">
                                    <button type="button" class="add-option" title="Add Option">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="option-con">
                                <input type="checkbox" disabled>
                                <textarea class="option" rows=1 placeholder="Option" required></textarea>
                                <div class="option-actions">
                                    <button type="button" class="add-option" title="Add Option">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <textarea class="explanation" rows=1 placeholder="Add an explanation (optional)"></textarea>
                        </div>
                    '
                ];
                break;
            case "boolean":
                $response = [
                    'answer_div' => '
                        <div class="col-md-10 bool" answer-div>
                            <i class="note">Forget not to tick the right option below</i>
                            <div class="option-con">
                                <input type="radio" value="1" />
                                <label>True</label>
                            </div>
                            <div class="option-con">
                                <input type="radio" value="0" />
                                <label>False</label>
                            </div>
                            <textarea class="explanation" rows=1 placeholder="Add an explanation (optional)"></textarea>
                        </div>
                    '
                ];
                break;
            case "written":
                $response = [
                    'answer_div' => '
                        <div class="col-md-10 written-div" answer-div>
                            <div class="option-con">
                                <textarea class="regex" rows=1 placeholder="Add word/phrase" required></textarea>
                                <div class="option-actions">
                                    <button type="button" class="add-option" title="Add phrase">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <textarea class="explanation" rows=1 placeholder="Add an explanation (optional)"></textarea>
                        </div>
                    '
                ];
                break;
            default:
                $response = [
                    'answer_div' => '
                        <div class="col-md-10 bg-danger text-white" answer-div>
                            <i>Options Modification Detected</i>
                        </div>
                    '
                ];
        }

        return response()->json($response);
    }

    public function add_option(){
        if(request()->header('X-My-Custom-Header') !== 'fetch-request'){ abort(404); }
        $option_type = request()->input("option_type");
        $field = '<textarea class="regex" rows=1 placeholder="Add word/phrase" required></textarea>';
        if($option_type !== "regex_field"){
            $field = '<input type="'.$option_type.'" disabled><textarea class="option" rows=1 placeholder="Option" required></textarea>';
        }

        $response = [
            'content' => '
                <div class="option-con">
                    '.$field.'
                    <div class="option-actions">
                        <button type="button" class="remove-option" title="Remove option">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                            </svg>
                        </button>
                        <button type="button" class="add-option" title="Add Option">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            ',
            'remove' => '
                <button type="button" class="remove-option" title="Remove option">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                    </svg>
                </button>
            ',
        ];
        
        return response()->json($response);
    }

    public function add_quiz_form(){
        if(request()->header('X-My-Custom-Header') !== 'fetch-request'){ abort(404); }
        try{
            $data  = request()->input('data_object');
            $title = Quiz_versions::where([
                'title'   => $data['title'],
                'version_number' => 1
            ])->first();

            if ($title) {
                throw ValidationException::withMessages([
                    'title' => ['Quiz title already exists']
                ]);
            }
            //Create first the quiz version
            $quiz_version = [
                "title"                   => strip_tags($data['title']),
                "title-translation"       => strip_tags($data['title-translation']),
                "description"             => strip_tags($data['description']),
                "description-translation" => strip_tags($data['description-translation']),
                "created_by"              => Auth::id()
            ];

            //Insert version on quiz table
            DB::beginTransaction();
            $quiz_version_id = DB::table('quiz_versions')->insertGetId($quiz_version);

            //Insert the version_id on quizzes
            $url = $data['fullUrl'];
            $parsedUrl = parse_url($url, PHP_URL_QUERY);
            parse_str($parsedUrl, $queryParams);
            $topic = isset($queryParams['topic']) ? Crypt::decrypt($queryParams['topic']) : null;
            $quiz = [
                "current_title" => $quiz_version['title'],
                "version_id"    => $quiz_version_id,
                "topic_id"      => $topic
            ];

            $quiz_id = DB::table('quizzes')->insertGetId($quiz);

            //Update the quiz id on the recently added quiz_version
            DB::table('quiz_versions')
                ->where('id',$quiz_version_id)
                ->update(['quiz_id' => $quiz_id]);
            
            $question    = $this->filter_keys($data,"question");
            $q_types     = $this->filter_keys($data,"type");
            
            foreach($question as $index => $value):
                $i = $index+1;
                $explanation = $this->filter_keys($data,"explanation-".$i);
                $qv_array = [
                    "question"      => strip_tags($value),
                    "question_type" => strip_tags($q_types[$index]),
                    "explanation"   => strip_tags($explanation[0] ?? null),
                ];
                //create first the question version
                $qv_id = DB::table('quiz_questionnaire_versions')->insertGetId($qv_array);

                //insert version on quiz_questionnaire table
                $qq_array = [
                    "sort_number"     => $i,
                    "quiz_version_id" => $quiz_version_id,
                    "version_id"      => $qv_id
                ];
                $qq_id = DB::table('quiz_questionnaires')->insertGetId($qq_array);

                //update the question versions quiz_questionnaire_id
                DB::table('quiz_questionnaire_versions')
                    ->where('id',$qv_id)
                    ->update(['quiz_questionnaire_id' => $qq_id]);
                
                if ($q_types[$index] == "boolean") {
                    //only single option insertion if type is boolean
                    $qcov_id = DB::table('quiz_choice_option_versions')->insertGetId([
                        "option" => ($data["answer-$i"] == 1) ? true : false
                    ]);
                    
                    $qco_id = DB::table('quiz_choice_options')->insertGetId([
                        "quiz_questionnaire_version_id" => $qv_id,
                        "version_id"                    => $qcov_id
                    ]);
                    
                    DB::table('quiz_choice_option_versions')
                        ->where('id',$qcov_id)
                        ->update(['quiz_choice_option_id' => $qco_id]);
                }else {
                    $options = $this->filter_keys($data, "option-$i");
                    foreach($options as $option => $value):
                        $qcov_id = DB::table('quiz_choice_option_versions')->insertGetId([
                            "option" => strip_tags($value)
                        ]);
                        
                        $qco_id = DB::table('quiz_choice_options')->insertGetId([
                            "quiz_questionnaire_version_id" => $qv_id,
                            "version_id"                    => $qcov_id
                        ]);
                        
                        DB::table('quiz_choice_option_versions')
                            ->where('id',$qcov_id)
                            ->update(['quiz_choice_option_id' => $qco_id]);

                        if ($q_types[$index] !== "written") {
                            $answers = $this->filter_keys($data, "answer-$i");
                            foreach($answers as $answer => $answer_value):
                                if($value == $answer_value):
                                    DB::table('quiz_answers')->insert([
                                        'quiz_questionnaire_version_id' => $qv_id,
                                        'quiz_choice_option_version_id' => $qcov_id
                                    ]);
                                endif;
                            endforeach;
                        }
                    endforeach;
                }
            endforeach;
            
            DB::commit();

            $response = [
                "status" => "success",
                "message" => "Form added successfully "
            ];
        }catch (ValidationException $e) {
            DB::rollBack();
            $response = [
                'status' => 'validation',
                'message' => 'Validation error occurred.',
                'errors' => $e->errors()
            ];
        }catch (QueryException $e){
            DB::rollBack();
            $response = [
                'status'  => 'error',
                'message' => 'An unexpected query error occurred, please try again later.',
                'error'   => $e->getMessage()
            ];
        }catch(BaseException $e){
            DB::rollBack();
            $response = [
                'status'  => 'error',
                'message' => 'An unexpected base error occurred, please try again later.',
                'error'   => $e->getMessage()
            ];
        }
        return response()->json($response);
    }
}
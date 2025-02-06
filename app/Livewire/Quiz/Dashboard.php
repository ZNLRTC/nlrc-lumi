<?php

namespace App\Livewire\Quiz;

use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Quizzes\Quizzes;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_attempts;
use App\Models\Quizzes\Quiz_questionnaires;
use App\Models\Trainee;

use Exception as BaseException;
use Illuminate\Database\QueryException;

class Dashboard extends BaseComponent {
    public function copyLink($link) {
        $this->dispatch('copyLink', $link);
    }

    public function total_attempts($quiz_id){
        return Quiz_attempts::join('quiz_versions', 'quiz_attempts.quiz_version_id', '=', 'quiz_versions.id')
            ->where('quiz_versions.quiz_id', $quiz_id)
            ->count();
    }
    
    public function count_version($quiz_id){
        $versions  = array_filter($this->quiz_versions, function($version) use ($quiz_id){
            return $version['quiz_id'] == $quiz_id;
        });

        return count($versions);
    }

    public function archive($q_id){
        $quiz = [];

        foreach ($this->quiz as $key => $q):
            if ($q['id'] === $q_id):
                $archive_value = !$q['archive'];
                $q['archive']  = $archive_value;
            endif;
            $quiz[] = $q;
        endforeach;

        $this->quiz = $quiz;

        DB::beginTransaction();
        try{
            DB::table('Quizzes')
                ->where('id',$q_id)
                ->update(['archive' => $archive_value]);
            
            DB::commit();
        }catch (QueryException $e){
            DB::rollBack();
            $this->exceptions = [
                'status'  => 'error',
                'message' => 'An unexpected query error occurred, please try again later.',
                'error'   => $e->getMessage()
            ];
        }catch(BaseException $e){
            DB::rollBack();
            $this->exceptions = [
                'status'  => 'error',
                'message' => 'An unexpected base error occurred, please try again later.',
                'error'   => $e->getMessage()
            ];
        }

        if(count($this->exceptions) == 0):
            session()->flash('message', 'Status updated successfully!');
        else:
            session()->flash('message', $this->exceptions);
        endif;
        
    }

    public function render() {
        $total_attempts     = Quiz_attempts::count();
        $attempts_today     = DB::table('quiz_attempts')->where(DB::raw('DATE(created_at)'), '=', date('Y-m-d'))->count();
        $total_trainees     = Trainee::count();
        $total_participants = DB::table('quiz_attempts')->distinct('trainee_id')->count('trainee_id');
        $attempts           = Quiz_attempts::all()->toArray();
        $score              = 0;
        $questions          = 0;

        if($attempts):
            foreach($attempts as $attempt):
                $score     += $attempt['score'];
                $questions += count(Quiz_questionnaires::where('quiz_version_id',$attempt['quiz_version_id'])->get());
            endforeach;
            $rate = ($score/$questions)*100;
        else:
            $rate = 0;
        endif;

        return view(
            'livewire.quiz.dashboard',[
                'total_attempts' => $total_attempts,
                'attempts_today' => $attempts_today,
                'total_trainees' => $total_trainees,
                'total_participants' => $total_participants,
                'attempts' => $attempts,
                'rate' => $rate
            ]
        );
    }
}
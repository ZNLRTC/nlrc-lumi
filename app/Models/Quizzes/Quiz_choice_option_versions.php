<?php

namespace App\Models\Quizzes;

use App\Models\Base_Model;
use App\Models\Quizzes\Quiz_choice_options;
use Illuminate\Support\Facades\DB;

class Quiz_choice_option_versions extends Base_Model {
    protected $table = 'quiz_choice_option_versions';
    protected $fillable = [
        'option',
        'quiz_choice_option_id',
        'version_number'
    ];

    public function parent_option(){
        return $this->belongsTo(Quiz_choice_options::class,'quiz_choice_option_id');
    }

    public static function get_all_options_where($qv_id){
        return self::where("quiz_versions.id",$qv_id)
            ->join('quiz_choice_options', 'quiz_choice_option_versions.quiz_choice_option_id', '=', 'quiz_choice_options.id')
            ->join('quiz_questionnaire_versions', 'quiz_choice_options.quiz_questionnaire_version_id', '=', 'quiz_questionnaire_versions.id')
            ->join('quiz_questionnaires', 'quiz_questionnaire_versions.quiz_questionnaire_id', '=', 'quiz_questionnaires.id')
            ->join('quiz_versions', 'quiz_questionnaires.quiz_version_id', '=', 'quiz_versions.id')
            ->select(
                'quiz_questionnaires.quiz_version_id',
                'quiz_choice_options.quiz_questionnaire_version_id',
                'quiz_choice_option_versions.id',
                'quiz_choice_option_versions.option',
            )
            ->get()
            ->map(function($item) {
                $exists = DB::table('quiz_answers')
                    ->where('quiz_questionnaire_version_id', $item->quiz_questionnaire_version_id)
                    ->where('quiz_choice_option_version_id', $item->id)
                    ->exists();
                
                $item->checked = $exists ? 1 : null;
                return $item;
            });
    }

    public static function get_all_version_options($qv_id){
        return self::where("quiz_versions.id",$qv_id)
            ->join('quiz_choice_options', 'quiz_choice_option_versions.quiz_choice_option_id', '=', 'quiz_choice_options.id')
            ->join('quiz_questionnaire_versions', 'quiz_choice_options.quiz_questionnaire_version_id', '=', 'quiz_questionnaire_versions.id')
            ->join('quiz_questionnaires', 'quiz_questionnaire_versions.id', '=', 'quiz_questionnaires.version_id')
            ->join('quiz_versions', 'quiz_questionnaires.quiz_version_id', '=', 'quiz_versions.id')
            ->select(
                'quiz_questionnaires.quiz_version_id',
                'quiz_choice_options.quiz_questionnaire_version_id',
                'quiz_choice_option_versions.id',
                'quiz_choice_option_versions.option',
            )
            ->get()
            ->map(function($item) {
                $exists = DB::table('quiz_answers')
                    ->where('quiz_questionnaire_version_id', $item->quiz_questionnaire_version_id)
                    ->where('quiz_choice_option_version_id', $item->id)
                    ->exists();
                
                $item->checked = $exists ? 1 : null;
                return $item;
            });
    }

    public static function get_attempt_options($qv_id){
        return self::where("quiz_versions.id",$qv_id)
            ->join('quiz_choice_options', 'quiz_choice_option_versions.quiz_choice_option_id', '=', 'quiz_choice_options.id')
            ->join('quiz_questionnaire_versions', 'quiz_choice_options.quiz_questionnaire_version_id', '=', 'quiz_questionnaire_versions.id')
            ->join('quiz_questionnaires', 'quiz_questionnaire_versions.id', '=', 'quiz_questionnaires.version_id')
            ->join('quiz_versions', 'quiz_questionnaires.quiz_version_id', '=', 'quiz_versions.id')
            ->select(
                'quiz_questionnaires.quiz_version_id',
                'quiz_choice_options.quiz_questionnaire_version_id',
                'quiz_choice_option_versions.id',
                'quiz_choice_option_versions.option',
            )
            ->get();
    }
}

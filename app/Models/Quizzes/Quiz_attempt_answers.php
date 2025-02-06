<?php

namespace App\Models\Quizzes;

use App\Models\Base_Model;
use App\Models\Quizzes\Quiz_questionnaire_versions;

class Quiz_attempt_answers extends Base_Model {
    protected $table = 'quiz_attempt_answers';
    protected $fillable = [
        'quiz_attempt_id',
        'quiz_id',
        'quiz_questionnaires_id',
        'answer',
    ];

    public static function answers_array($attempt_id){
        return self::select('quiz_questionnaire_version_id', 'answer')
        ->where('quiz_attempt_id', $attempt_id)
        ->get()
        ->groupBy('quiz_questionnaire_version_id')
        ->map(function ($group, $quiz_questionnaire_version_id) {
            return [
                'quiz_questionnaire_version_id' => $quiz_questionnaire_version_id,
                'answer' => $group->pluck('answer')->toArray()
            ];
        })->toArray();
    }

    public function question(){
        return $this->belongsTo(Quiz_questionnaire_versions::class,'quiz_questionnaire_version_id');
    }
}

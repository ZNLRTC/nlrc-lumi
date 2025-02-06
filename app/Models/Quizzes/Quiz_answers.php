<?php

namespace App\Models\Quizzes;

use App\Models\Base_Model;
use App\Models\Quizzes\Quiz_questionnaire_versions;
use App\Models\Quizzes\Quiz_choice_option_versions;

class Quiz_answers extends Base_Model {
    protected $table = 'quiz_answers';
    protected $fillable = [
        'quiz_questionnaire_version_id',
        'quiz_choice_option_version_id'
    ];

    public function parent_question(){
        return $this->belongsTo(Quiz_questionnaire_versions::class,'quiz_questionnaire_version_id');
    }

    public function parent_option(){
        return $this->belongsTo(Quiz_choice_option_versions::class,'quiz_choice_option_version_id');
    }
}

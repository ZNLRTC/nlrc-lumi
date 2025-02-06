<?php

namespace App\Models\Quizzes;

use App\Models\Base_Model;
use App\Models\Quizzes\Quiz_questionnaire_versions;
use App\Models\Quizzes\Quiz_choice_option_versions;

class Quiz_choice_options extends Base_Model {
    protected $table = 'quiz_choice_options';
    protected $fillable = [
        'quiz_questionnaire_version_id',
        'version_id'
    ];

    public function parent_questionnaire(){
        return $this->belongsTo(Quiz_questionnaire_versions::class,'quiz_questionnaire_version_id');
    }

    public function latest_version(){
        return $this->belongsTo(Quiz_choice_option_versions::class,'version_id');
    }

    public function option_versions(){
        return $this->hasMany(Quiz_choice_option_versions::class,'quiz_choice_option_id');
    }
}

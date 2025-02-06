<?php

namespace App\Models\Quizzes;

use App\Models\Base_Model;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Quizzes\Quiz_questionnaire_versions;

class Quiz_questionnaires extends Base_Model {
    protected $table = 'quiz_questionnaires';
    protected $fillable = [
        'sort_number',
        'quiz__version_id',
        'version_id',
        'archive'
    ];

    public function quiz_version() {
        return $this->belongsTo(Quiz_versions::class,'quiz_version_id');
    }

    public function latest_version(){
        return $this->belongsTo(Quiz_questionnaire_versions::class,'version_id');
    }

    public function questionnaire_versions() {
        return $this->hasMany(Quiz_questionnaire_versions::class,'quiz_questionnaire_id');
    }

    public static function get_all_questionnaires_where($qv_id){
        return self::where("quiz_version_id",$qv_id)
            ->with("latest_version")
            ->orderBy("sort_number","asc")
            ->get();
    }
}

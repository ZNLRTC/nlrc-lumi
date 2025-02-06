<?php
namespace App\Models\Quizzes;
use App\Models\Base_Model;
use App\Models\Quizzes\Quiz_questionnaires;

class Quiz_questionnaire_versions extends Base_Model {
    protected $table = 'quiz_questionnaire_versions';
    protected $fillable = [
        'question',
        'quiz_questionnaire_id',
        'question_type',
        'version_number',
        'explanation'
    ];

    public function parent_questionnaire(){
        return $this->belongsTo(Quiz_questionnaires::class,'quiz_questionnaire_id');
    }

}
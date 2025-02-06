<?php
namespace App\Models\Quizzes;
use App\Models\Base_Model;
use App\Models\User;
use App\Models\Quizzes\Quizzes;
use App\Models\Quizzes\Quiz_attempts;
use App\Models\Quizzes\Quiz_questionnaires;

class Quiz_versions extends Base_Model {
    protected $table = 'quiz_versions';
    protected $fillable = [
        'quiz_id',
        'title',
        'title-translation',
        'description',
        'description-translation',
        'version_number',
    ];

    public function parent_quiz() {
        return $this->belongsTo(Quizzes::class,'quiz_id');
    }

    public function creator() {
        return $this->belongsTo(User::class,'created_by');
    }

    public function attempts() {
        return $this->hasMany(Quiz_attempts::class,'quiz_version_id');
    }
    
    public function questionnaires() {
        return $this->hasMany(Quiz_questionnaires::class,'quiz_version_id');
    }

}

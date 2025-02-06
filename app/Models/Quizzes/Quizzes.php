<?php
namespace App\Models\Quizzes;
use App\Models\Quizzes\Quiz_versions;
use App\Models\Base_Model;

class Quizzes extends Base_Model {
    protected $table = 'quizzes';
    protected $fillable = [
        'current_title',
        'version_id',
        'archive',
        'topic_id'
    ];

    public function quiz_versions(){
        return $this->hasMany(Quiz_versions::class, 'quiz_id');
    }
}
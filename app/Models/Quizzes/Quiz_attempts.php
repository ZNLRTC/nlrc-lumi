<?php

namespace App\Models\Quizzes;

use App\Models\Base_Model;
use App\Models\Trainee;
use App\Models\Quizzes\Quiz_versions;
use Illuminate\Support\Facades\DB;

class Quiz_attempts extends Base_Model {
    protected $table = 'quiz_attempts';
    protected $fillable = [
        'trainee_id',
        'quiz_id',
        'score',
    ];

    public function quiz_version() {
        return $this->belongsTo(Quiz_versions::class, 'quiz_version_id');
    }

    public function trainee(){
        return $this->belongsTo(Trainee::class, 'trainee_id', 'user_id');
    }
}

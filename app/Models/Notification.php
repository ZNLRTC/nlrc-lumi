<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public function getTraineeGroupAttribute() {
        if ($this->notifiable_type == 'App\Models\Trainee') {
            $trainee = Trainee::find($this->notifiable_id);

            return $trainee->activeGroup->group->group_code;
        }
    }
}

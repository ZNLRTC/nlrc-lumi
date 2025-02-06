<?php

namespace App\Models;

use App\Models\Trainee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineesVerifiedRequest extends Model
{
    use HasFactory;

    const CREATED_AT = 'requested_at';
    const UPDATED_AT = 'verified_at';

    protected $fillable = [
        'trainee_id',
        'staff_user_id', // This is not a foreign key since this is nullable on create
        'is_verified'
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class, 'trainee_id');
    }

    public function getStaffUserNameAttribute() {
        if ($this->staff_user_id) {
            $user = User::find($this->staff_user_id);

            return $user['name'];
        } else {
            return 'N/A';
        }
    }
}

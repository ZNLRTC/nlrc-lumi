<?php

namespace App\Models;

use App\Events\ReceiveAnnouncementEvent;
use App\Mail\SendAnnouncementEmail;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'thumbnail_image_path'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getThumbnailImageAttribute()
    {
        if ($this->thumbnail_image_path) {
            return Storage::disk('announcements')->url($this->thumbnail_image_path);
        }
    }

    // Custom model functions
    // At the moment, this function requires a join with the users table to access the user_id and email fields
    protected function send_announcement_then_notify(Announcement $announcement, Trainee $trainee, bool $is_priority): void
    {
        // NOTE: Queueing mails is currently untested
        /*
        Mail::to($trainee->email)->queue(new SendAnnouncementEmail($announcement, $trainee));

        Mail::to($trainee->email)->send(new SendAnnouncementEmail($announcement, $trainee));
        */

        $trainee->notify(new AnnouncementNotification($announcement, $is_priority));

        broadcast(new ReceiveAnnouncementEvent($trainee->user_id)); // Trigger an event
    }
}

<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $announcement;
    public $is_priority;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement, bool $is_priority)
    {
        $this->announcement = $announcement;
        $this->is_priority = $is_priority;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'is_priority' => $this->is_priority,
            'user_id' => $this->announcement->user_id, // Staff user id who made the announcement
            'title' => $this->announcement->title,
            'description' => $this->announcement->description,
            'created_at' => $this->announcement->created_at,
            'updated_at' => $this->announcement->updated_at
        ];
    }

    /**
     * Get the notification's database type.
     *
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'announcement-sent';
    }
}

<?php

namespace App\Notifications;

use App\Models\Meetings\MeetingsOnCall;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingsOnCallNotification extends Notification
{
    use Queueable;

    public $meetings_on_call;

    /**
     * Create a new notification instance.
     */
    public function __construct(MeetingsOnCall $meetings_on_call)
    {
        $this->meetings_on_call = $meetings_on_call;
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
            'meetings_on_call_id' => $this->meetings_on_call->id,
            'meeting_link' => $this->meetings_on_call->meeting_link,
            'start_time' => $this->meetings_on_call->start_time,
            'end_time' => $this->meetings_on_call->end_time
        ];
    }

    /**
     * Get the notification's database type.
     *
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'meetings-on-call-sent';
    }
}

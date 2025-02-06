<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReceiveAnnouncementEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('receive-announcement.' .$this->user_id)];
    }
}

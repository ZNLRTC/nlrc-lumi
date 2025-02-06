<?php

namespace App\Livewire\WebsocketTest;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Events\WebsocketTests\TestChatMessage;
use Illuminate\Support\Facades\Auth;

class WSTestComponent extends Component
{
    public $message = '';
    public $convo = [];

    public function sendMessage()
    {
        // $this->validate([
        //     'message' => 'required|string|max:255',
        // ]);

        broadcast(new TestChatMessage($this->message, Auth::id()));

        $this->message = '';
    }

    #[On('echo:test-chat,WebsocketTests\TestChatMessage')]
    public function onMessageArrival($event)
    {
        $message = $event['message'];
        $userId = $event['userId'];

        $this->convo[] = [
            'message' => $message,
            'userId' => $userId,
        ];
    }

    #[On('echo:test-channel,WebsocketTests\Ping')]
    public function onPing($event)
    {
        dd($event);
    }

    public function render()
    {
        return view('livewire.websocket-test.w-s-test-component');
    }
}

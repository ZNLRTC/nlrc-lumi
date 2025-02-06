<div>
    <h1>For testing websocket connection. These are not saved anywhere.</h1>
    <div>
        @foreach($convo as $msg)
            <div class="message">
                <strong>User {{ $msg['userId'] }}:</strong> {{ $msg['message'] }}
            </div>
        @endforeach
    </div>

    <div>
        <x-input type="text" wire:model="message" placeholder="Type here..."/>
        <x-button wire:click="sendMessage">Send</x-button>
    </div>

    <p><a href="{{ route('ping') }}">Broadcast ping</a></p>
</div>
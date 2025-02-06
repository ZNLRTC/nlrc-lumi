@props([
    'trainee_notifications',
    'trainee_notifications_unread_count' => 0,
    'trainee_notifications_unread_count_is_overlap' => false
])

<x-dropdown align="right" width="64">
    <x-slot name="trigger">
        <button class="flex items-center pt-0.5">
            <x-heroicon-o-bell class="h-6 w-auto text-nlrc-blue-700 hover:fill-nlrc-blue-600 dark:text-nlrc-blue-500 dark:hover:fill-nlrc-blue-600" />
            <sup>
                <span class="inline-flex py-0.5 px-1 rounded-full max-h-8 max-w-8 min-h-4 min-w-4 items-center justify-center text-white relative end-2 {{ $trainee_notifications_unread_count > 0 ? 'bg-red-600 dark:bg-red-400' : 'bg-nlrc-blue-300 dark:bg-nlrc-blue-600' }}">{{ $trainee_notifications_unread_count }}{{ $trainee_notifications_unread_count_is_overlap ? '+' : '' }}</span>
            </sup>
        </button>
    </x-slot>

    <x-slot name="content">
        @forelse ($trainee_notifications as $notification)
            <div class="flex text-sm justify-start p-2 gap-2 border-b border-nlrc-blue-200 dark:border-nlrc-blue-900 hover:bg-nlrc-blue-200 dark:hover:bg-nlrc-blue-500 first-of-type:rounded-t-md {{ !$notification->read_at ? 'bg-nlrc-blue-100 dark:bg-nlrc-blue-500 hover:bg-nlrc-blue-400 dark:hover:bg-nlrc-blue-600' : '' }}">
                @if ($notification->type == 'announcement-sent')
                    <div class="pt-1 flex-none">
                        @if (!$notification->read_at)
                            <svg wire:click.prevent="set_is_read('{{ $notification->id }}')" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-red-400 size-4 hover:fill-red-300 cursor-pointer">
                                <title>Mark as read</title>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-nlrc-blue-500 size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z" />
                            </svg>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col right">
                        <a wire:click.prevent="set_is_read('{{ $notification->id }}')" wire:navigate href="{{ route('announcements.detail', ['id' => $notification->data['announcement_id']]) }}">
                            <h2 class="dark:text-white {{ !$notification->read_at ? 'font-bold' : '' }}">{{ $notification->data['title'] }}</h2>
                            <p class="indent-1 text-slate-600 dark:text-slate-400">
                                {{ strip_tags(\Illuminate\Support\Str::limit($notification->data['description'], '32', ' (...)')) }}
                            </p>

                            {{-- created_at field is based on announcement's created_at field and not notification's created_at field --}}
                            <small class="mt-2 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($notification->data['created_at'])->diffForHumans(\Carbon\Carbon::now(), [
                                    'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                    'options' => \Carbon\Carbon::JUST_NOW | \Carbon\Carbon::NO_ZERO_DIFF | \Carbon\Carbon::ONE_DAY_WORDS
                                ]) }}
                            </small>
                        </a>
                    </div>
                @elseif ($notification->type == 'meetings-on-call-sent')
                    <div class="pt-1 flex-none">
                        @if (!$notification->read_at)
                            <svg wire:click.prevent="set_is_read('{{ $notification->id }}')" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-red-400 size-4 hover:fill-red-300 cursor-pointer">
                                <title>Mark as read</title>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-nlrc-blue-500 size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z" />
                            </svg>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col right">
                        <a wire:navigate wire:click.prevent="set_is_read('{{ $notification->id }}')" href="{{ route('dashboard') }}">
                            <h2 class="dark:text-white {{ !$notification->read_at ? 'font-bold' : '' }}">An on-call meeting is about to start in 30 minutes. Check your trainee dashboard for details</h2>

                            <small class="mt-2 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans(\Carbon\Carbon::now(), [
                                    'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                    'options' => \Carbon\Carbon::JUST_NOW | \Carbon\Carbon::NO_ZERO_DIFF | \Carbon\Carbon::ONE_DAY_WORDS
                                ]) }}
                            </small>
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <p class="p-2 text-sm dark:text-white">You have no notifications.</p>
        @endforelse

        @if (count($trainee_notifications) > 0)
            <a wire:navigate href="{{ route('notifications') }}" class="block p-2 text-center text-sm text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-400">See more notifications</a>
        @endif
    </x-slot>
</x-dropdown>

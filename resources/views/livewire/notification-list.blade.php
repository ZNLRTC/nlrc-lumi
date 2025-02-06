<div>
    <x-banner />

    <div class="min-h-screen bg-nlrc-blue-50 dark:bg-nlrc-blue-900">
        <livewire:NavMenu />

        <header class="bg-nlrc-blue-100 dark:bg-nlrc-blue-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">Notifications</h2>
            </div>
        </header>

        <main>
            <div class="py-10">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="flex flex-col p-4 space-y-2 overflow-hidden shadow-xl bg-white dark:bg-nlrc-blue-800 sm:rounded-lg">
                        @forelse ($trainee_notifications as $notification)
                            @if ($notification->type == 'announcement-sent')
                                <a wire:navigate wire:click="set_is_read('{{ $notification->id }}')" href="{{ route('announcements.detail', ['id' => $notification->data['announcement_id']]) }}">
                            @elseif ($notification->type == 'meetings-on-call-sent')
                                <a wire:navigate wire:click="set_is_read('{{ $notification->id }}')" href="{{ route('dashboard') }}">
                            @endif

                                <div class="p-2 border-b border-nlrc-blue-200 hover:bg-nlrc-blue-400 dark:hover:bg-nlrc-blue-700 dark:border-nlrc-blue-900 sm:p-4 {{ !$notification->read_at ? 'bg-nlrc-blue-200 dark:bg-nlrc-blue-900' : '' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <h2 class="dark:text-white {{ !$notification->read_at ? 'font-bold' : '' }}">
                                            @if ($notification->type == 'announcement-sent')
                                                <div class="nlrc markdown">
                                                    @if (str_word_count($notification->data['description']) > 200)
                                                        <div class="line-clamp-3 text-justify">{!! Markdown::parse($notification->data['description']) !!}</div>
                                                    @else
                                                        <div class="text-justify">{!! Markdown::parse($notification->data['description']) !!}</div>
                                                    @endif
                                                </div>
                                            @elseif ($notification->type == 'meetings-on-call-sent')
                                                An on-call meeting is about to start in 30 minutes. Check your trainee dashboard for details
                                            @endif
                                        </h2>

                                        <x-custom.unread-circle :is_read="$notification->read_at" />
                                    </div>

                                    @if ($notification->type == 'announcement-sent')
                                        {{-- created_at field is based on announcement's created_at field and not notification's created_at field --}}
                                        <small class="block mt-2 dark:text-slate-400">
                                            {{ \Carbon\Carbon::parse($notification->data['created_at'])->diffForHumans(\Carbon\Carbon::now(), [
                                                'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                                'options' => \Carbon\Carbon::JUST_NOW | \Carbon\Carbon::NO_ZERO_DIFF | \Carbon\Carbon::ONE_DAY_WORDS
                                            ]) }}
                                        </small>
                                    @elseif ($notification->type == 'meetings-on-call-sent')
                                        <small class="block mt-2 dark:text-slate-400">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans(\Carbon\Carbon::now(), [
                                                'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                                'options' => \Carbon\Carbon::JUST_NOW | \Carbon\Carbon::NO_ZERO_DIFF | \Carbon\Carbon::ONE_DAY_WORDS
                                            ]) }}
                                        </small>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p class="px-2 dark:text-white">You have no notifications.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

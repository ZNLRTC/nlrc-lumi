<div class="p-4">
    @if ($is_filtered || count($announcements) > 0)
        <x-button wire:click="$toggle('show_filter_modal')" class="p-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 0 1 .628.74v2.288a2.25 2.25 0 0 1-.659 1.59l-4.682 4.683a2.25 2.25 0 0 0-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 0 1 8 18.25v-5.757a2.25 2.25 0 0 0-.659-1.591L2.659 6.22A2.25 2.25 0 0 1 2 4.629V2.34a.75.75 0 0 1 .628-.74Z" clip-rule="evenodd" />
            </svg>
        </x-button>
    @endif

    @if ($is_filtered)
        <div class="flex gap-2 mt-4">
            @foreach ($filters as $key => $filter_value)
                @if ($filter_value == 1)
                    <span class="p-2 border-2 border-solid rounded-xl w-fit bg-nlrc-blue-100 border-nlrc-blue-400 dark:bg-nlrc-blue-800 dark:text-white">
                        <span class="mr-1">Show: {{ ucfirst($key) }}</span>

                        <x-loading-indicator
                            :loader_color_bg="'fill-slate-900 dark:fill-white'"
                            :loader_color_spin="'fill-red-500'"
                            :showText="false"
                            :size="4"
                            target="reset_filters('{{ $key }}')"
                        />

                        <button wire:click.prevent="reset_filters('{{ $key }}')" wire:loading.remove wire:target="reset_filters('{{ $key }}')" class="text-red-600 dark:text-red-300" title="Reset filters">&times;</button>
                    </span>
                @endif
            @endforeach
        </div>
    @endif

    <x-modal wire:model="show_filter_modal">
        <div class="flex justify-between px-6 py-4 border-b border-b-slate-200 text-xl">
            <h2 class="dark:text-white">Filter Announcements</h2>

            <button wire:click="$toggle('show_filter_modal')" class="dark:text-white">&times;</button>
        </div>

        <form wire:submit.prevent="filter_announcements" class="px-6 py-2 space-y-2">
            <div class="flex justify-end">
                <x-loading-indicator
                    :loader_color_bg="'fill-slate-900 dark:fill-white'"
                    :loader_color_spin="'fill-red-500'"
                    :showText="false"
                    :size="6"
                    :target="'reset_filters'"
                />

                <a wire:click.prevent="reset_filters" wire:loading.remove wire:target="reset_filters" href="#" class="ml-2 text-red-600 dark:text-red-400 hover:text-red-400 dark:hover:text-red-600">Reset filters</a>
                <span wire:loading wire:target="reset_filters" class="ml-2 text-red-600 dark:text-red-400 hover:text-red-400 dark:hover:text-red-600">Resetting filters</span>
            </div>

            <div class="flex items-center gap-2">
                <x-input wire:model="filter_is_priority" wire:loading.attr="disabled" wire:target="show_filter_modal" type="checkbox" id="filter-prioritized-announcements" class="disabled:bg-nlrc-blue-400" id="filter_is_priority" />
                <label class="dark:text-white" for="filter-prioritized-announcements">Show only prioritized announcements</label>
            </div>

            <div class="flex items-center gap-2">
                <x-input wire:model="filter_is_read" wire:loading.attr="disabled" wire:target="show_filter_modal" type="checkbox" id="filter-unread-announcements" class="disabled:bg-nlrc-blue-400" id="filter_is_read" />
                <label class="dark:text-white" for="filter-unread-announcements">Show only unread announcements</label>
            </div>

            <x-button wire.loading.attr="disabled" class="block bg-green-600 active:bg-green-400 focus:bg-green-400 hover:bg-green-400 dark:bg-green-400 dark:active:bg-green-600 dark:focus:bg-green-600 dark:hover:bg-green-600 mb-4" type="submit">
                <span wire:loading wire:target="filter_announcements">Filtering</span>
                <span wire:loading.remove wire:target="filter_announcements">Filter</span>
            </x-button>
        </form>
    </x-modal>

    <div wire:loading.flex wire:target="filter_announcements, reset_filters" class="flex-col items-center my-4 dark:text-white">
        <x-loading-indicator
            :loader_color_bg="'fill-slate-900 dark:fill-white'"
            :loader_color_spin="'fill-slate-900 dark:fill-white'"
            :size="20"
            :text="'Filtering. Please wait...'"
        />
    </div>

    @if (count($announcements) > 0)
        <div wire:loading.remove wire:target="filter_announcements, reset_filters">
            @foreach ($announcements as $announcement)
                <div class="mb-4 mt-2 rounded border border-nlrc-blue-200 dark:border-nlrc-blue-900 first:mt-4">
                    <div class="p-2 border-b border-nlrc-blue-200 bg-nlrc-blue-200 dark:bg-nlrc-blue-900 dark:border-nlrc-blue-900 sm:p-4">
                        <div class="flex justify-between">
                            <h2 class="dark:text-slate-400 {{ !$announcement->read_at ? 'font-bold' : '' }}">
                                <a wire:navigate wire:click="set_is_read('{{ $announcement->id }}')" href="{{ route('announcements.detail', ['id' => $announcement->data['announcement_id']]) }}" class="text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-400">{{ $announcement->data['title'] }}</a>

                                <x-custom.unread-circle :is_read="$announcement->read_at" :tooltip="'baby Unread announcement'" />
                            </h2>

                            @if ($announcement->data['is_priority'])
                                <div class="flex items-center priority-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="text-red-400 size-4">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 dark:text-slate-200">Priority</span>
                                </div>
                            @endif
                        </div>

                        <p class="text-slate-600 dark:text-slate-400 text-sm">Posted by {{ \App\Models\User::find($announcement->data['user_id'])->name }} on <span class="font-bold">{{ \Carbon\Carbon::parse($announcement->data['created_at'])->format('D, F j, Y \a\t h:i A') }}</span></p>
                    </div>

                    {{-- The "nlrc markdown" classes add formatting for lists and paragraph breaks that Tailwind otherwise overrides for markdown content --}}
                    <div class="p-2 sm:p-4 nlrc markdown">
                        @if (str_word_count($announcement->data['description']) > 200)
                            <div class="mt-2 indent-2 text-justify line-clamp-3 dark:text-slate-400">{!! Markdown::parse($announcement->data['description']) !!}</div>
                            <a wire:navigate wire:click="set_is_read('{{ $announcement->id }}')" href="{{ route('announcements.detail', ['id' => $announcement->data['announcement_id']]) }}" class="d-inline-block mb-4 text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-400" title="Click me to read more">Read more</a>
                        @else
                            <div class="mt-2 indent-2 text-justify dark:text-slate-400">{!! Markdown::parse($announcement->data['description']) !!}</div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{ $announcements->withQueryString()->links() }}
        </div>
    @else
        <p class="dark:text-white">No announcements found.</p>
    @endif
</div>

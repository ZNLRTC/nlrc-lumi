<x-page-section x-data="{ open: false }">

    <button @click="open = !open" class='w-full flex flex-row justify-between items-center'>
        <h2 class='text-lg'>Your meetings</h2>
        <div :class="{'rotate-180': open, 'rotate-0': !open}" class="transition-transform duration-500" title="Open and close the meeting listing">
            <x-heroicon-o-chevron-down class='text-nlrc-blue-500 dark:text-white h-5 stroke-2' />
        </div>
    </button>

    <div x-show="open" x-collapse>
        @forelse($unit->meetings as $meeting)

            @include('meetings.partials.meetings-of-unit', ['meeting' => $meeting])

        @empty

            <div class="text-sm text-slate-600 dark:text-slate-300 p-2 border border-nlrc-blue-100 bg-nlrc-blue-50 dark:border-nlrc-blue-700 dark:bg-nlrc-blue-900 rounded">You have no recorded meetings about this unit yet.</div>

        @endforelse

    </div>

</x-page-section>
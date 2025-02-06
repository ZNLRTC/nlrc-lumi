<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Log a new meeting
        </h2>
    </x-slot>

    <x-page-section>
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            <livewire:meetings.log-meeting />

            <livewire:meetings.latest-meetings />
        </div>
    </x-page-section>

</x-app-layout>
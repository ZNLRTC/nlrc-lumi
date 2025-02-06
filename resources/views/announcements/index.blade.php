<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">Announcements</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-nlrc-blue-800 overflow-hidden shadow-xl sm:rounded-lg">
                <livewire:announcements.AnnouncementList />
            </div>
        </div>
    </div>
</x-app-layout>

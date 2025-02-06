<x-app-layout>
    <x-slot name="header">

        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
            <a wire:navigate class='text-slate-500 hover:text-slate-400 dark:text-slate-400 dark:hover:text-slate-300' href="{{ route('announcements.index') }}">Announcements</a> <span class='ps-0 sm:ps-2 text-slate-400 dark:text-slate-600'>&raquo;</span> <span class="ps-0 sm:ps-2">{{ $current_announcement->title }}</span>
        </h2>

    </x-slot>

    <x-page-section >
        <livewire:announcements.AnnouncementDetail :announcement_id="$current_announcement->id" />
    </x-page-section>
</x-app-layout>

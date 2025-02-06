<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
            <a class='text-slate-500 hover:text-slate-400 dark:text-slate-400 dark:hover:text-slate-300' href="{{ route('courses.index', ['course' => $course->slug]) }}">{{ $course->name }}</a> 
            <span class='ps-0 sm:ps-2 text-slate-400 dark:text-slate-600'>&raquo;</span> 
            <a class="ps-0 sm:ps-2 text-slate-500 hover:text-slate-400 dark:text-slate-400 dark:hover:text-slate-300" href="{{ route('units.index', ['course' => $course->slug, 'unit' => $unit->slug]) }}">{{ $unit->name }}</a>
            <span class='ps-0 sm:ps-2 text-slate-400 dark:text-slate-600'>&raquo;</span>
            <span class='ps-0 sm:ps-2'>Submit an assignment</span>
        </h2>
    </x-slot>

    <livewire:meetings.assignments.submit-assignment :assignment="$assignment"/>

</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
            "{{ $course->name }}" course
        </h2>
    </x-slot>

    <livewire:course.main :course="$course" />

</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
            <a class='text-slate-500 hover:text-slate-400 dark:text-slate-400 dark:hover:text-slate-300' href="{{ route('exams.index') }}">Tests, exams, and assessments</a> <span class='ps-0 sm:ps-2 text-slate-400 dark:text-slate-600'>&raquo;</span> <span class="ps-0 sm:ps-2">{{ $exam->name }}</span>
        </h2>
    </x-slot>

    @if (strtolower($type) === 'test')
        <livewire:exams.show-test :exam="$exam" :type="$type" />
    @elseif (strtolower($type) === 'assessment')
        <livewire:exams.show-test :exam="$exam" :type="$type" />
    @elseif (strtolower($type) === 'exam')
        <livewire:exams.show-exam :exam="$exam" :type="$type" />
    @else
        <x-page-section>
            <p>Invalid exam type.</p>
        </x-page-section>
    @endif

</x-app-layout>
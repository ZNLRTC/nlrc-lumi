<x-app-layout>
    <x-slot name="header">
        <div class='flex justify-between items-center'>
            <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
                <a class='text-slate-500 hover:text-slate-400 dark:text-slate-400 dark:hover:text-slate-300' href="{{ route('courses.index', ['course' => $course->slug]) }}">{{ $course->name }}</a> <span class='ps-0 sm:ps-2 text-slate-400 dark:text-slate-600'>&raquo;</span> <span class="ps-0 sm:ps-2">{{ $unit->name }}</span>
            </h2>

            <div class="flex flex-col sm:flex-row flex-1 justify-end items-end sm:items-center gap-1 sm:gap-2">
                @if ($previousUnit)
                    <a href="{{ route('units.index', ['course' => $course->slug, 'unit' => $previousUnit->slug]) }}" class="flex justify-center items-center text-sm rounded text-white group">
                        <div class="flex justify-center items-center p-1 bg-nlrc-blue-500 rounded-s group-hover:bg-nlrc-blue-600 dark:bg-nlrc-blue-950 dark:group-hover:bg-nlrc-blue-700/50">
                            <x-heroicon-s-chevron-left class="h-5 w-auto"/>
                        </div>
                        <p class="px-2 py-1 rounded-e bg-nlrc-blue-400 dark:bg-nlrc-blue-900 group-hover:bg-nlrc-blue-500 dark:group-hover:bg-nlrc-blue-700 active:bg-nlrc-blue-500 dark:active:bg-nlrc-blue-900">{{ strtolower($previousUnit->name) }}</p>
                    </a>
                @else
                    <span></span>
                @endif
            
                @if ($nextUnit)
                <a href="{{ route('units.index', ['course' => $course->slug, 'unit' => $nextUnit->slug]) }}" class="flex justify-center items-center text-sm rounded text-white group">
                    <p class="px-2 py-1 rounded-s bg-nlrc-blue-400 dark:bg-nlrc-blue-900 group-hover:bg-nlrc-blue-500 dark:group-hover:bg-nlrc-blue-700 active:bg-nlrc-blue-500 dark:active:bg-nlrc-blue-900">{{ strtolower($nextUnit->name) }}</p>
                    <div class="flex justify-center items-center p-1 bg-nlrc-blue-500 rounded-e group-hover:bg-nlrc-blue-600 dark:bg-nlrc-blue-950 dark:group-hover:bg-nlrc-blue-700/50">
                        <x-heroicon-s-chevron-right class="h-5 w-auto"/>
                    </div>
                </a>
                @else
                    <span></span>
                @endif
            </div>
        <div>
    </x-slot>

    <livewire:course.show-unit :course="$course" :unit="$unit" />

</x-app-layout>
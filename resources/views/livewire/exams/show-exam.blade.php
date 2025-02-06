<x-page-section>
    <div class='w-full overflow-x-auto'>
        <div class='flex flex-col md:flex-row justify-between items-start md:items-center mb-4 py-4 gap-4'>
            @if ($exam->exam_paper_url)
                <p>Download the exam papers on Google Drive <a href="{{ $exam->exam_paper_url }}" class="text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-300" target="_blank" rel="noopener noreferrer">here</a>.</p>
            @endif

            <div class="me-2 flex gap-2 items-center sticky left-0">
                @foreach ($exam->sections as $section)
                    <x-button wire:click="toggleSectionVisibility({{ $section->id }})">
                        {{ $sectionVisibility[$section->id] ? 'Hide' : 'Show' }} {{ Str::after($section->name, ', ') }}
                    </x-button>
                @endforeach
                <div wire:dirty.remove class='ms-6 text-nlrc-green-100 dark:text-green-600'><span class="font-bold">&#x2713;</span> All changes saved</div>
                <div wire:dirty class='ms-6 text-slate-500 dark:text-slate-400'>Saving...</div>
            </div>
        </div>
    
        <table class="table-fixed text-left w-full">
            <thead>
                <tr>
                    <th class="pe-4 w-44 min-w-44 sticky left-0 z-10 bg-gradient-to-r from-white dark:from-slate-800 from-90%">Trainee</th>
                    @foreach ($exam->sections as $section)
                        @if ($sectionVisibility[$section->id])
                            @foreach ($section->tasks as $task)
                                <th class='w-36'>{{ ucfirst($task->short_name) }}<br>
                                    {{-- Hide decimals if there aren't any in the total --}}
                                    <span class='text-slate-500 dark:text-slate-400 text-sm font-normal'>max. {{ number_format($task->max_score, $task->max_score == (int) $task->max_score ? 0 : 2) }} points</span>
                                </th>
                            @endforeach
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($sortedTrainees as $trainee)
                    <tr class='border-b border-nlrc-blue-200 dark:border-nlrc-blue-900'>
                        <td class='sticky left-0 z-10 bg-gradient-to-r from-white dark:from-slate-800 from-90%'>
                            {{ $trainee->pivot->trainee_alias ?? '' }}
                            <span class='text-slate-500 dark:text-slate-400'>{{ "$trainee->last_name, $trainee->first_name" }}</span>
                        </td>
                        @foreach ($exam->sections as $section)
                            @if ($sectionVisibility[$section->id])
                                @foreach ($section->tasks as $task)
                                    <td class='text-sm relative'>
                                        <x-select wire:model.live.debounce.500ms="scores.task_{{ $task->id }}_trainee_{{ $trainee->id }}" class='text-sm my-1' wire:dirty.class='bg-sky-200 text-sky-300 dark:bg-sky-800 dark:text-sky-900'>
                                            <option value="">Points</option>
                                            @for ($i = $task->min_score; $i <= $task->max_score; $i += 0.5)
                                                @if ($i == 0)
                                                    <option value="{{ number_format($i, 2) }}">0</option>
                                                @else
                                                    <option value="{{ number_format($i, 2) }}">{{ $i }}</option>
                                                @endif
                                            @endfor
                                        </x-select> 
                                        <div class="absolute flex top-3 left-9" wire:dirty wire:target='scores.task_{{ $task->id }}_trainee_{{ $trainee->id }}'><x-loading-indicator size="6" :showText="false" /></div>
                                    </td>
                                @endforeach
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-page-section>
<div class='text-sm grid grid-cols-1 gap-x-2 xl:gap-x-4 gap-y-1 px-3 py-4 w-full min-w-max'>
    @foreach ($groupedTaskScores as $sectionName => $scores)

        @php
            $isPassing = $sectionPercentages[$sectionName] >= $scores->first()->examTask->sections->first()->passing_percentage;
            $passingClass = $isPassing ? 'text-nlrc-green-100 dark:text-green-400' : 'text-red-600 dark:text-red-400';

            $hasMissingGrade = $scores->contains(function ($score) {
                return $score->score == 0 && isset($score->note);
            });
            $headingClass = $hasMissingGrade ? 'text-orange-700 dark:text-orange-300' : $passingClass;
            $headingText = $hasMissingGrade ? "$sectionName (not fully graded)" : $sectionName;
        @endphp

        <div x-data="{ open: false }" class="grid grid-cols-3 lg:grid-cols-6 place-content-start">

            {{-- Section headings --}}
            @if ($uniqueSectionsCount > 1)
                <div @click="open = !open" class="flex items-center gap-1 col-span-full lg:col-span-3 cursor-pointer">
                    @if ($isPassing)
                        <x-heroicon-m-check-circle class="h-4 w-4 {{ $passingClass }}" />
                    @elseif ($hasMissingGrade)
                        <x-heroicon-m-exclamation-triangle class="h-4 w-4 {{ $headingClass }}" />
                    @else
                        <x-heroicon-m-x-circle class="h-4 w-4 {{ $passingClass }}" />
                    @endif
                    <h3 class='{{ $headingClass }}'>{{ $headingText }}</h3>
                </div>
            @endif
            
            {{-- Icon only if the exam only has one section --}}
            <div @click="open = !open" class="col-span-full {{ $uniqueSectionsCount > 1 ? 'lg:col-span-2' : 'lg:col-span-3' }} {{ $passingClass }} cursor-pointer flex gap-x-2 items-center">
                @if ($uniqueSectionsCount == 1)
                    @if ($isPassing)
                        <x-heroicon-m-check-circle class="h-4 w-4 {{ $passingClass }}" />
                    @elseif ($hasMissingGrade)
                        <x-heroicon-m-exclamation-triangle class="h-4 w-4 {{ $headingClass }}" />
                    @else
                        <x-heroicon-m-x-circle class="h-4 w-4 {{ $passingClass }}" />
                    @endif
                @endif

                {{-- Percentage of the section --}}
                <p><span class='font-bold'>{{ number_format($sectionPercentages[$sectionName], $sectionPercentages[$sectionName] == (int) $sectionPercentages[$sectionName] ? 0 : 2) }}%</span> / {{ number_format($scores->first()->examTask->sections->first()->passing_percentage, $scores->first()->examTask->sections->first()->passing_percentage == (int) $scores->first()->examTask->sections->first()->passing_percentage ? 0 : 2) ?? 'N/A' }}%</p>
            </div>

            {{-- Tottal score of the section --}}
            <div @click="open = !open" class="col-span-full {{ $uniqueSectionsCount > 1 ? 'lg:col-span-1' : 'lg:col-span-3' }} {{ $passingClass }} cursor-pointer w-full flex justify-between">
                <p><span class='font-bold'>{{ $sectionTotals[$sectionName] }}</span> / {{ $sectionMaxScores[$sectionName] }}</p>
                <x-heroicon-m-chevron-down x-show="!open" class='h-5 w-6' />
                <x-heroicon-m-chevron-up x-show="open" class='h-5 w-6' />
            </div>

            {{-- Individual scores, collapsed by default --}}
            <template x-if="open">
                <div class="col-span-full grid grid-cols-2 md:grid-cols-6 gap-x-1">
                    @foreach ($scores as $score)
                        @php
                            $task = $score->examTask;
                            $displayName = $task->short_name ?: $task->name;
                        @endphp

                        <div class="md:col-span-2">– {{ $displayName }} 
                            @if (isset($score->note))
                            <span class="{{ $headingClass }}">({{ $score->note }})</span>
                        @endif

                        </div>
                        
                        <div x-data="{ showInput: false }" @keydown.escape.window="showInput = false" @score-saved.window="showInput = false">
                            <div @click="showInput = !showInput">
                                <span x-show="!showInput">
                                    {{ number_format($score->score, $score->score == (int) $score->score ? 0 : 2) }} 
                                    <span class="text-slate-400 dark:text-slate-500">/ {{ number_format($task->max_score, $task->max_score == (int) $task->max_score ? 0 : 2) }}</span>
                                </span>
                            </div>
                            <div class='flex flex-col gap-0'>
                                <div x-show="showInput" class="flex items-center gap-1">
                                    <x-input id="score_{{ $score->examTask->id }}" wire:keydown.enter="saveScore({{ $score->examTask->id }})" wire:model="scores.{{ $score->examTask->id }}" />
                                    <button wire:click="saveScore({{ $score->examTask->id }})" class="rounded p-1 border border-nlrc-blue-200 dark:border-nlrc-blue-600 bg-nlrc-blue-100 dark:bg-nlrc-blue-800 hover:bg-nlrc-blue-200 dark:hover:bg-nlrc-blue-700">
                                        <x-heroicon-o-check class="w-3 h-3" />
                                    </button>
                                    <button @click.prevent="showInput = false" class="rounded p-1 border border-nlrc-blue-200 dark:border-nlrc-blue-600 bg-nlrc-blue-100 dark:bg-nlrc-blue-800 hover:bg-nlrc-blue-200 dark:hover:bg-nlrc-blue-700">
                                        <x-heroicon-o-x-mark class="w-3 h-3" />
                                    </button>
                                </div>
                                <x-input-error for="scores.{{ $score->examTask->id }}" class="text-red-500 text-xs mt-1" />
                            </div>
                        </div>

                    @endforeach
                </div>
            </template>
        </div>
    @endforeach
</div>
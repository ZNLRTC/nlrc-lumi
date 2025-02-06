@php
    use App\Enums\Exams\ExamAttemptStatus;
@endphp

<div>
    @if ($trainee)
        <div class="flex flex-col md:flex-row gap-4">
            <div class="grow-0">
                @if ($trainee_profile_photo)
                    <p class="font-bold">Profile Photo:</p>

                    <img src="{{ $trainee->user->profilePhotoUrl() }}" class="w-32 h-32" alt="User profile photo" title="User profile photo" />
                @else
                    <div class="w-32 h-32 rounded p-2 text-center flex items-center bg-nlrc-blue-200 text-slate-600 dark:bg-nlrc-blue-900 dark:text-slate-400">No profile photo available.</div>
                @endif

                <p class="mt-2 md:text-center">{{ $trainee->last_name }}, {{ $trainee->first_name }}</p>
            </div>

            <div class="grow">
                @if ($pastAttempts->isEmpty())
                    <p>The trainee has not taken this {{ $this->type }} before.</p>
                @else
                    @foreach ($pastAttempts as $attempt)
                        <div class="text-sm border rounded flex flex-col md:mt-6 md:mb-4 {{ $attempt->status === ExamAttemptStatus::PASSED ? 'border-green-200 bg-green-50 dark:border-green-900 dark:bg-green-800' : 'border-red-200 dark:border-red-950 bg-red-50 dark:bg-red-800' }}">
                            <div class="flex flex-row rounded-t p-2 {{ $attempt->status === ExamAttemptStatus::PASSED ? 'bg-green-200 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                <div class='w-full'>
                                    <h3>Attempt on {{ \Carbon\Carbon::parse($attempt->date)->format('D, F j, Y') }}: {{ $attempt->status->getLabel() }}</h3>
                                    <p>Instructor: {{ $attempt->instructor->name }}</p>
                                </div>

                                <div class="flex-initial">
                                    @if (Auth::id() == $attempt->instructor_id && $attempt->created_at->greaterThanOrEqualTo(\Carbon\Carbon::now()->subHours(2)))
                                        <x-danger-button title="Delete" wire:click="deleteAttempt({{ $attempt->id }})">
                                            <x-heroicon-o-trash class="h-4"/>                                  
                                        </x-button>
                                    @endif
                                </div>
                            </div>

                            <div class="p-2">
                                @if ($sectionsWithTasks->isEmpty())
                                    <p>No sections found for this {{ $this->type }}.</p>
                                @else
                                    @foreach ($sectionsWithTasks as $sectionWithTasks)
                                        @if ($totalSections > 1)
                                            <h4 class='text-lg mt-2'>Section: {{ $sectionWithTasks['section']->name }}</h4>
                                        @endif
                                        @if ($sectionWithTasks['tasks']->isEmpty())
                                            <p>No tasks found for this section.</p>
                                        @else
                                            <ul class='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2'>
                                                @foreach ($sectionWithTasks['tasks'] as $task)
                                                    @php
                                                        $score = $task->examTaskScores->where('exam_attempt_id', $attempt->id)->first();
                                                    @endphp
                                                    <li class="flex flex-row sm:flex-col w-full gap-2 sm:gap-0">
                                                        <p>{{ $task->name }}:</p>
                                                        <p>
                                                            <span class='font-semibold'>{{ $score->score ?? '0' }} / {{ $task->max_score }}</span> <span class='text-slate-500 dark:text-slate-300'>({{ $score->score ? round(($score->score / $task->max_score) * 100, 2) : '0' }}%)</span>
                                                        </p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                @endif
                            </div>

                            @if ($attempt->feedback || $attempt->internal_notes)
                                <div class="p-2 border-t {{ $attempt->status === ExamAttemptStatus::PASSED ? 'border-green-200 dark:border-green-900' : 'border-red-200 dark:border-red-800' }}">
                                    <p>Feedback: <span class="break-all italic">{{ $attempt->feedback }}</span></p>
                                    <p>Notes: <span class="break-all italic">{{ $attempt->internal_notes}}</span></p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @else
        <p>No trainee selected.</p>
    @endif
</div>

@php
    use App\Enums\Exams\ExamAttemptStatus;
@endphp

<div>
    @if ($sectionsWithTasks)
        <form
            x-data="{
                attemptFeedbackCharacterCount: 0,
                attemptNotesCharacterCount: 0,
                changeFeedbackCharacterCount() {
                    this.attemptFeedbackCharacterCount = $wire.attemptFeedback.length;
                },
                changeNotesCharacterCount() {
                    this.attemptNotesCharacterCount = $wire.attemptNotes.length;
                }
            }"
            x-init="
                $wire.on('status-updated', dispatchedData => {
                    attemptFeedbackCharacterCount = dispatchedData.attemptFeedbackText.length;
                });

                $wire.on('grades-submitted', function() {
                    attemptFeedbackCharacterCount = 0;
                    attemptNotesCharacterCount = 0;
                });
            "
            wire:submit.prevent="submitGrades"
        >
            @foreach ($sectionsWithTasks as $sectionWithTasks)
                @if ($totalSections > 1)
                    <h3 class="text-lg my-4">{{ $sectionWithTasks['section']->name }}</h3>
                @endif

                <p class="mb-4">Fields marked with <x-required /> are required. The trainee must pass all tasks marked with <x-required /><x-required /> or they cannot pass the {{ $this->type }}, regardless of other points.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
                    @foreach ($sectionWithTasks['tasks'] as $task)
                        <div>
                            <x-label for="task-{{ $task->id }}">
                                {{ $task->name }}
                                @if ($task->passing_score > 0)
                                    <x-required /><x-required />
                                @else
                                    <x-required />
                                @endif
                            </x-label>
                            <x-select id="task-{{ $task->id }}" wire:model.live="grades.{{ $task->id }}" :disabled="$isSaved || $status === ExamAttemptStatus::ABSENT->value">
                                <option value="">Select points</option>
                                @for ($i = $task->min_score; $i <= $task->max_score; $i += 0.5)
                                    @if ($i == 0)
                                        <option value="{{ number_format($i, 2) }}">0</option>
                                    @else
                                        <option value="{{ number_format($i, 2) }}">{{ $i }}</option>
                                    @endif
                                @endfor
                            </x-select>
                            <x-input-error for="grades.{{ $task->id }}" />
                        </div>
                    @endforeach

                </div>
                <div class='mt-4 pb-4 border-b border-nlrc-blue-200 dark:border-nlrc-blue-900'>
                    <p>
                        Points: {{ $selectedSectionTotals[$sectionWithTasks['section']->id] ?? 0 }} / {{ $sectionTotals[$sectionWithTasks['section']->id] }} 
                        ({{ number_format(($selectedSectionTotals[$sectionWithTasks['section']->id] ?? 0) / $sectionTotals[$sectionWithTasks['section']->id] * 100, 1) }}%, 
                        {{ number_format($sectionWithTasks['section']->passing_percentage, $sectionWithTasks['section']->passing_percentage == (int) $sectionWithTasks['section']->passing_percentage ? 0 : 2) }}% needed to pass)
                    </p>
                </div>
            @endforeach

            <div class="mt-4">
                <x-label value="{{ __('Choose outcome') }}" :is_required="true" for="status" />
                @foreach (ExamAttemptStatus::cases() as $status)
                    @if ($status !== ExamAttemptStatus::PENDING)
                        <label class="me-4 {{ $isSaved ? 'text-slate-500 dark:text-slate-400' : '' }}">
                            <x-input :disabled="$isSaved" wire:model.live="status" type="radio" name="status" value="{{ $status->value }}" />
                            {{ $status->getLabel() }}
                        </label>
                    @endif
                @endforeach
                <x-input-error for="status" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-label value="{{ __('Feedback for the trainee') }}" for="attemptFeedback" />
                    <x-textarea wire:model="attemptFeedback" x-on:keyup="changeFeedbackCharacterCount" :disabled="$isSaved"></x-textarea>
                    <small class="text-xs text-slate-700 dark:text-slate-300"><span x-text="attemptFeedbackCharacterCount"></span> / 500 characters</small>
                    <x-input-error for="attemptFeedback" />
                </div>

                <div>
                    <x-label value="{{ __('Internal notes') }}" for="attemptNotes" />
                    <x-textarea wire:model="attemptNotes" x-on:keyup="changeNotesCharacterCount" :disabled="$isSaved"></x-textarea>
                    <small class="text-xs text-slate-700 dark:text-slate-300"><span x-text="attemptNotesCharacterCount"></span> / 500 characters</small>
                    <x-input-error for="attemptNotes" />
                </div>
            </div>

            @if (session('grades-saved'))
                <div class="text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-900 p-2 rounded my-2">
                    {{ session('grades-saved')}}
                </div>
            @endif
            @if (session('already-passed'))
                <div class="text-red-700 bg-red-50 dark:text-red-400 dark:bg-red-900 p-2 rounded my-2">
                    {{ session('already-passed')}}
                </div>
            @endif

            <x-button class="mt-4" type="submit" :disabled="$isSaved">Submit result</x-button>
            <x-loading-indicator class="ms-4" target="submitGrades" text="saving..." />

        </form>

    @else
        <p>No trainee selected or this {{ $this->type }} has no sections.</p>
    @endif
</div>

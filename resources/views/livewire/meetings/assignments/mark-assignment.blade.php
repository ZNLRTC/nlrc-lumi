@php
    use App\Enums\Assignments\SubmissionStatus;
@endphp

<div class="p-4">
    @if ($this->assignments)
        @forelse ($this->assignments as $assignment)
            <h2 class="text-lg">{{ $assignment->name }}</h2>

            <div class="p-2 mb-2 border rounded border-nlrc-blue-200 dark:border-nlrc-blue-900 md:p-4 nlrc markdown">
                {!! $assignment->instructions !!}
            </div>

            @foreach ($assignment->submissions as $submission)

                {{-- Submission not checked or is being edited --}}
                @if ($submission->submission_status === SubmissionStatus::NOT_CHECKED || $submission->id === $editingSubmissionId )

                    <div class="my-4 border rounded border-nlrc-blue-200 dark:border-nlrc-blue-900">
                        <div class="flex flex-col items-start justify-between px-2 py-2 text-sm md:flex-row md:items-center md:px-4 bg-nlrc-blue-100 dark:bg-nlrc-blue-900">
                            <p>Submission #{{ $loop->iteration }}</p>
                            <div class="text-xs text-left md:text-right text-slate-600 dark:text-slate-300">
                                <p>Submitted on {{ \Carbon\Carbon::parse($submission->submitted_at)->inUserTimezone()->format('D, M j, Y, H:i') }}</p>
                                @if ($submission->edited_at)
                                    <p>Edited on {{ \Carbon\Carbon::parse($submission->edited_at)->inUserTimezone()->format('D, M j, Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="px-2 py-2 md:px-4">
                            <p>{{ $submission->submission }}</p>

                            <form wire:submit.prevent="saveFeedback({{ $submission->id }})">
                                <div class="grid gap-0 mt-4 md:grid-cols-3 md:gap-2">

                                    <div class="flex flex-col gap-2" wire:model="status.{{ $submission->id }}">
                                        <p class="text-sm text-slate-600 dark:text-slate-300">Status<x-required/></p>
                                        <div>
                                            <x-input type="radio" id="completed-{{ $submission->id }}" value="completed" name="status-{{ $submission->id }}"/>
                                            <label for="completed-{{ $submission->id }}">Completed</label>
                                        </div>
                                    
                                        <div>
                                            <x-input type="radio" id="incomplete-{{ $submission->id }}" value="incomplete" name="status-{{ $submission->id }}"/>
                                            <label for="incomplete-{{ $submission->id }}">Incomplete</label>
                                        </div>
                                        <x-input-error for="status.{{ $submission->id }}"/>
                                    </div>

                                    <div class="col-span-2">
                                        <p class="text-sm text-slate-600 dark:text-slate-300">Feedback (0–500 characters)</p>
                                        <x-textarea wire:model="feedback.{{ $submission->id }}"></x-textarea>
                                        <x-input-error for="feedback.{{ $submission->id }}"/>
                                    </div>
                                
                                </div>
                                <x-button class="mt-4" type="submit">Save</x-button>

                                @if($editingSubmissionId == $submission->id)
                                    <x-button class="mt-4 ms-1 md:ms-2" wire:click="cancelEdit">Cancel</x-button>
                                @endif
                            </form>
                        </div>
                    </div>

                {{-- Submission checked already --}}
                @else

                    @php
                        $colorClasses = $submission->submission_status == SubmissionStatus::COMPLETED ? 'bg-green-50 border-green-200 dark:bg-nlrc-blue-800 dark:border-green-900' : 'bg-red-50 border-red-200 dark:bg-nlrc-blue-800 dark:border-red-900';
                        $headerColors = $submission->submission_status == SubmissionStatus::COMPLETED ? 'bg-green-100 border-green-200 dark:bg-green-900 dark:border-green-900' : 'bg-red-100 border-red-200 dark:bg-red-900 dark:border-red-900';
                        $textColorClass = $submission->submission_status == SubmissionStatus::COMPLETED ? 'text-green-700 dark:text-green-400' :  'text-red-700 dark:text-red-400';
                    @endphp

                    <div class="my-4 border {{ $colorClasses }} rounded">
                        <div class="flex flex-col gap-1 md:gap-4 md:flex-row justify-between items-start md:items-center py-2 px-2 md:px-4 {{ $headerColors }}">
                            <div class="flex flex-row justify-between w-full text-sm grow md:w-auto md:flex-col md:justify-start">
                                <p>Submission #{{ $loop->iteration }}</p>

                                <p class="{{ $textColorClass }}">{{ $submission->submission_status === SubmissionStatus::INCOMPLETE ? 'Incomplete' : 'Completed' }}Incomplete</p>
                            </div>
                            <div class="text-xs text-left grow md:text-right text-slate-600 dark:text-slate-300">
                                <p>Checked by {{ $submission->instructor->name }} on {{ \Carbon\Carbon::parse($submission->checked_at)->inUserTimezone()->format('D, M j, Y, H:i') }}</p>
                                @if ($submission->edited_at)
                                    <p>Submitted on {{ \Carbon\Carbon::parse($submission->submitted_at)->inUserTimezone()->format('D, M j, Y, H:i') }}, edited {{ \Carbon\Carbon::parse($submission->edited_at)->inUserTimezone()->format('D, M j, Y, H:i') }}</p>
                                @else
                                    <p>Submitted on {{ \Carbon\Carbon::parse($submission->submitted_at)->inUserTimezone()->format('D, M j, Y, H:i') }}</p>
                                @endif
                            </div>
                            <div class="grow-0">
                                @if($submission->checked_at->diffInDays(now()) <= 2 && $submission->instructor_id == auth()->user()->id)
                                    <x-danger-button wire:click="edit({{ $submission->id }})" title="Remove current feedback and set the assignment as not checked">Revert</x-danger-button>
                                @endif
                            </div>
                        </div>

                        <div class="p-2 md:p-4">
                            <p>{{ $submission->submission }}</p>

                            @if ($submission->feedback)
                                <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">Feedback</p>
                                <p>{{ $submission->feedback }}</p>
                            @endif
                        </div>
                    </div>

                @endif
            @endforeach
        @empty
            <p>No submissions for any assignments in this unit.</p>
        @endforelse
    @else

        <div>
            <p>No submissions for any assignments in this unit.</p>
        </div>

    @endif
</div>
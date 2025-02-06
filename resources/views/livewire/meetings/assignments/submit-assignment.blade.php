@php
    use App\Enums\Assignments\SubmissionStatus;
@endphp

<div>
    <x-page-section>
        <p class="text-sm text-slate-600 dark:text-slate-400">Task:</p>
        <h3 class="text-lg">{{ $assignment->name }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 my-2">Instructions:</p>
        <div class="rounded border border-nlrc-blue-200 dark:border-nlrc-blue-900 p-2 md:p-4 mb-2 markdown">
            {!! $instructions !!}
        </div>
        
        <form class="mt-2 pt-2 md:pt-4">
            @if ($latestSubmission)
            
                @if ($latestSubmission->submission_status == SubmissionStatus::NOT_CHECKED)
                    <div class="flex gap-2 rounded p-2 mb-2 bg-green-50 text-green-900 dark:bg-green-900 dark:text-green-500">
                        <div class="grow-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-nlrc-green-100 dark:stroke-nlrc-green-300 stroke-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <p>Your submission has been saved. You may edit the submission until an instructor marks it as checked.</p>
                   </div>
                    <x-textarea disabled="{{ $isEditing ? false : true }}" type="text" wire:model="submission"></x-textarea>
                    @if ($latestSubmission->edited_at)
                        <div class="mt-2 text-sm text-slate-600 dark:text-slate-400">edited on {{ \Carbon\Carbon::parse($latestSubmission->edited_at)->format('D, M j, Y, H:i') }}</div>
                    @else
                        <div class="mt-2 text-sm text-slate-600 dark:text-slate-400">submitted on {{ \Carbon\Carbon::parse($latestSubmission->submitted_at)->format('D, M j, Y, H:i') }}</div>
                    @endif
                    <x-input-error for="submission"/>
                    <x-input-error for="updateSubmission"/>
                    @if ($isEditing)
                        <x-button class="mt-4" type="button" wire:click.prevent="updateSubmission">Save Changes</x-button>
                        <x-button class="mt-4 ms-2 bg-nlrc-blue-400 dark:bg-nlrc-blue-600" type="button" wire:click.prevent="cancelEditing">Cancel</x-button>
                    @else
                        <x-button class="mt-4" type="button" wire:click.prevent="startEditing">Edit</x-button>
                    @endif

                @elseif ($latestSubmission->submission_status == SubmissionStatus::INCOMPLETE)
                    <div class="flex gap-2 rounded p-2 mb-2 bg-red-50 text-red-800 dark:bg-red-900 dark:text-red-300">
                        <div class="grow-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-red-600 dark:stroke-red-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                        </div>
                        <p>Your previous submission was marked as incomplete. You have to make another submission for your next meeting. Type a new submission below. See the bottom of the page for earlier submissions and possible feedback.</p>
                    </div>
                    <x-textarea type="text" wire:model="submission" placeholder="Type your submission here..."></x-textarea>
                    <x-input-error for="submission"/>
                    <x-button class="mt-4" type="submit" wire:click.prevent="submit">Submit</x-button>
                
                @elseif ($latestSubmission->submission_status == SubmissionStatus::COMPLETED)
                    <div class="flex gap-2 rounded p-2 mb-2 bg-green-50 text-green-900 dark:bg-green-900 dark:text-green-500">
                        <div class="grow-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-nlrc-green-100 dark:stroke-nlrc-green-300 stroke-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <p>This assignment has been marked as completed. You do not have to submit another version.</p>
                    </div>
                @endif

            @else
                <x-textarea type="text" placeholder="Type your submission here..." wire:model="submission"></x-textarea>
                <x-input-error for="submission"/>
                <x-button class="mt-4" type="submit" wire:click.prevent="submit">Submit</x-button>
            @endif

        </form>
    </x-page-section>

    @if($pastCheckedSubmissions->count() > 0)
        <x-page-section>

            <h2 class="text-lg">Past checked submissions</h2>
                @foreach($pastCheckedSubmissions as $submission)

                @php
                    $colorClasses = $submission->submission_status == SubmissionStatus::COMPLETED ? 'bg-green-50 border-green-200 dark:bg-green-800 dark:border-green-900' : ($submission->submission_status == SubmissionStatus::INCOMPLETE ? 'bg-red-50 border-red-200 dark:bg-red-800 dark:border-red-900' : 'bg-nlrc-blue-50 border-nlrc-blue-200 dark:bg-nlrc-blue-600 dark:border-nlrc-blue-700');
                    $headerColors = $submission->submission_status == SubmissionStatus::COMPLETED ? 'bg-green-100 border-green-200 dark:bg-green-900 dark:border-green-900' : ($submission->submission_status == SubmissionStatus::INCOMPLETE ? 'bg-red-100 border-red-200 dark:bg-red-900 dark:border-red-900' : 'bg-nlrc-blue-100 border-nlrc-blue-200 dark:bg-nlrc-blue-700 dark:border-nlrc-blue-700');
                    $textColorClass = $submission->submission_status == SubmissionStatus::COMPLETED ? 'text-green-700 dark:text-green-400' : ($submission->submission_status == SubmissionStatus::INCOMPLETE ? 'text-red-700 dark:text-red-400' : 'text-slate-700 dark:text-slate-200');
                @endphp

                    <div class="md:grid md:grid-cols-2 divide-y md:divide-x rounded border {{ $headerColors }}">
                        <div class="col-span-full flex flex-row justify-between text-sm {{ $headerColors }}">
                            <p class="p-2">Submission #{{ $loop->iteration }}</p>
                            <p class="p-2 text-xs text-right text-slate-600 dark:text-slate-300">Submitted on 
                                @if ($submission->edited_at)
                                    {{ \Carbon\Carbon::parse($submission->edited_at)->format('M j, Y, H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($submission->submitted_at)->format('M j, Y, H:i') }}
                                @endif    
                                <br>Checked by {{ $submission->instructor->name }} on {{ \Carbon\Carbon::parse($submission->checked_at)->format('M j, Y, H:i') }}
                            </p>
                        </div>
                        <div class="col-span-full flex gap-1 {{ $colorClasses }}">
                            <p class="p-2 text-sm text-slate-600 dark:text-slate-300">Status:</p>
                            <div class="p-2">
                                @if ($submission->submission_status == SubmissionStatus::COMPLETED)
                                    <p class="{{ $textColorClass }}">Completed</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">This submission meets the requirements of the assignment.</p>
                                @else
                                    <p class="{{ $textColorClass }}">Incomplete</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">This submission does not meet the requirements of the assignment.</p>
                                @endif
                            </div>
                        </div>
                        <div class="{{ $colorClasses }} {{ $submission->feedback ? '' : 'col-span-full' }}">
                            <p class="px-2 pt-2 text-sm text-slate-600 dark:text-slate-300">Submission:</p>
                            <p class="px-2 pb-2">{{ $submission->submission }}</p>
                        </div>
                        
                        @if ($submission->feedback)
                            <div class="{{ $colorClasses }}">
                                <p class="px-2 pt-2 text-sm text-slate-600 dark:text-slate-300 md:m-0">Instructor's feedback:</p>
                                <p class="px-2 pb-2">{{ $submission->feedback }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
        </x-page-section>
    @endif

</div>

<div class="col-span-3">
    <div class="mb-4 border border-nlrc-blue-100 dark:border-nlrc-blue-900 rounded flex flex-col">
        <div class="p-4 flex flex-row border-b border-nlrc-blue-50 bg-nlrc-blue-50 dark:border-nlrc-blue-900 dark:bg-nlrc-blue-900">
            <p class="pe-4 text-nlrc-blue-500 dark:text-slate-400 font-black text-lg grow-0">1.</p>
            <div class="w-full">
                <p>Find a trainee by typing their email or name. Then select the trainee.</p>
            </div>
        </div>

        <div class="p-4 ms-8 dark:bg-nlrc-blue-800">
            <x-input type="text" wire:model.live.debounce.1000ms="search" placeholder="Search..." />
            <x-loading-indicator :target="'search'" :text="'Searching...'" :size="6" class="ms-4" />

            @if ($search)
                <div wire:loading.remove wire:target="search" class="my-4 grid grid-cols-2 md:grid-cols-4 grid-flow-row auto-rows-min text-sm md:divide-y md:divide-slate-100 dark:md:divide-slate-600">
                    @forelse ($trainees as $trainee)
                        {{-- If the top border is not defined, divide-y is gonna slap the border for the group and the button for the first row anyway --}}
                        <div class="col-span-2 border-t border-nlrc-blue-100 dark:border-nlrc-blue-600 py-1">
                            <p>{{ $trainee->last_name. ', ' .$trainee->first_name }}</p>
                            <span class="text-slate-600 dark:text-slate-400">{{ optional($trainee->user)->email }}</span>
                        </div>
                        <div class="py-1">{{ optional($trainee->activeGroup->group)->group_code }}</div>
                        <div class="py-1 w-full justify-self-end md:justify-self-start">
                            <x-secondary-button wire:click="selectTrainee({{ $trainee->id }})">Select</x-secondary-button>
                        </div>
                    @empty
                        <p>No trainees found.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    {{-- Course->Unit->Meeting selector --}}
    <div class="mb-4 border border-nlrc-blue-100 dark:border-nlrc-blue-900 rounded flex flex-col">
        <div class="p-4 flex flex-row border-b border-nlrc-blue-50 bg-nlrc-blue-50 dark:border-nlrc-blue-900 dark:bg-nlrc-blue-900">
            <p class="pe-4 text-nlrc-blue-500 dark:text-slate-400 font-black text-lg grow-0">2.</p>
            <div class="w-full">
                <p>Select the course, the unit, and the meeting. Load written assignments for the unit.</p>
            </div>
        </div>

        <div wire:loading.flex wire:target="selectTrainee" class="flex-col items-center my-4 dark:text-white">
            <x-loading-indicator :text="'Loading courses and units...'" :size="20" />
        </div>

        <div wire:loading.remove wire:target="selectTrainee" class="p-4 ms-8 dark:bg-nlrc-blue-800">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <x-select wire:change="selectCourse($event.target.value)">
                    @foreach ($courses as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select>
                
                <x-select wire:model="selectedUnitId" wire:change="selectUnit($event.target.value)">
                    @foreach ($units as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select>
            
                <x-select wire:model="selectedMeetingId">
                    @foreach ($meetings as $id => $meeting)
                        <option value="{{ $meeting->id }}">{{ $meeting->description }}</option>
                    @endforeach
                </x-select>
            </div>
            <x-input-error for="selectedMeetingId"/>
            <x-button :disabled="!$selectedUnitId" class="mt-2" wire:click="openAssignment">Load assignments</x-button>
            <x-loading-indicator :target="'openAssignment'" :size="5" class="ms-4" />
        </div>
    </div>

    {{-- Meeting details --}}

    <div class="mb-4 border border-nlrc-blue-100 dark:border-nlrc-blue-900 rounded flex flex-col">
        <div class="p-4 flex flex-row border-b border-nlrc-blue-50 bg-nlrc-blue-50 dark:border-nlrc-blue-900 dark:bg-nlrc-blue-900">
            <p class="pe-4 text-nlrc-blue-500 dark:text-slate-400 font-black text-lg grow-0">3.</p>
            <div class="w-full">
                <p>Enter details of the meeting{{ $traineeFullName ? " with $traineeFullName" : '' }}.</p>
            </div>
        </div>

        <div class="p-4 ms-8 dark:bg-nlrc-blue-800">
            <form wire:submit.prevent="createMeeting">
                <div class="grid grid-cols-1 xl:grid-cols-4 gap-2">
                    <div class="flex flex-col md:flex-row gap-2 col-span-full">
                        <div class="basis-1/3">
                            <x-label value="{{ __('Date of the meeting') }}" is_required="true" for="meetingDate" />
                            <x-input wire:model="meetingDate" type="date" />
                            <small class="block text-xs text-slate-700 dark:text-slate-300">Format: MM/DD/YYYY</small>
                            <x-input-error for="meetingDate" />
                        </div>
                
                        <div class="basis-2/3 grow">
                            <x-label value="{{ __('Outcome') }}" is_required="true" for="selectedMeetingStatusId" />
                            <div class="flex flex-col xl:flex-row xl:gap-1">
                                @foreach ($meetingStatuses as $status)
                                    <div class="flex items-center gap-1 ps-2">
                                        <x-input type="radio" id="status{{ $status->id }}" wire:model="selectedMeetingStatusId" value="{{ $status->id }}" />
                                        <label for="status{{ $status->id }}">{{ $status->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error class="ml-2" for="selectedMeetingStatusId" />
                        </div>
                    </div>
                
                    <div x-data="{
                            meetingFeedbackCharacterCount: 0,
                            changeFeedbackCharacterCount() {
                                this.meetingFeedbackCharacterCount = $wire.meetingFeedback.length;
                            }
                        }"
                        class="col-span-full lg:col-span-2"
                    >
                        <x-label value="{{ __('Feedback for the trainee') }}" is_required="true" for="meetingFeedback" />
                        <x-textarea wire:model="meetingFeedback" x-on:keyup="changeFeedbackCharacterCount"></x-textarea>
                        <small class="text-xs text-slate-700 dark:text-slate-300"><span x-text="meetingFeedbackCharacterCount"></span> / 500 characters</small>
                        <x-input-error for="meetingFeedback" />
                    </div>
            
                    <div x-data="{
                            meetingNotesCharacterCount: 0,
                            changeNotesCharacterCount() {
                                this.meetingNotesCharacterCount = $wire.meetingNotes.length;
                            }
                        }"
                        class="col-span-full lg:col-span-2"
                    >
                        <x-label value="{{ __('Internal notes') }}" for="meetingNotes" />
                        <x-textarea wire:model="meetingNotes" x-on:keyup="changeNotesCharacterCount"></x-textarea>
                        <small class="text-xs text-slate-700 dark:text-slate-300"><span x-text="meetingNotesCharacterCount"></span> / 500 characters</small>
                        <x-input-error for="meetingNotes" />
                    </div>
                
                    <div class="col-span-full">
                        <div class="flex flex-row gap-4">
                            <x-button :disabled="!$selectedTraineeId" wire:loading.attr="disabled">Save</x-button>

                            <x-loading-indicator :target="'createMeeting'" :text="'Saving meeting'" :size="5" />
                            @if (session('meeting-saved'))
                                <div class="text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-900 p-2 rounded">
                                    {{ session('meeting-saved')}}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-modal-with-bg id="assigmentMarkingModal" maxWidth="max" wire:model="showAssignmentModal">
        <livewire:meetings.assignments.mark-assignment />
    </x-modal-with-bg>
</div>

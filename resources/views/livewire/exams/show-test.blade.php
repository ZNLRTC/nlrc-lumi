<x-page-section>
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
                            @if (in_array($trainee->eligibility_status, ['Eligible', 'View only']))
                                <x-secondary-button wire:click="selectTrainee({{ $trainee->id }})" x-on:click="$dispatch('load-trainees-exam-details', { traineeId: {{ $trainee->id }}})">
                                    {{ $trainee->eligibility_status === 'Eligible' ? 'Select' : 'Edit' }}
                                </x-secondary-button>
                            @else
                                <span class="text-red-500 dark:text-red-400">{{ $trainee->eligibility_status }}</span>
                            @endif
                        </div>
                    @empty
                        <p>No trainees found.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    <div class="mb-4 border border-nlrc-blue-100 dark:border-nlrc-blue-900 rounded flex flex-col">
        <div class="p-4 flex flex-row border-b border-nlrc-blue-50 bg-nlrc-blue-50 dark:border-nlrc-blue-900 dark:bg-nlrc-blue-900">
            <p class="pe-4 text-nlrc-blue-500 dark:text-slate-400 font-black text-lg grow-0">2.</p>
            <div class="w-full">
                <p>Check details and past attempts.</p>
            </div>
        </div>

        <div wire:loading.flex wire:target="selectTrainee" class="flex-col items-center my-4 dark:text-white">
            <x-loading-indicator :text="'Loading trainee details...'" :size="20" />
        </div>

        <div wire:loading.remove wire:target="selectTrainee" class="p-4 ms-8 dark:bg-nlrc-blue-800">
            <livewire:exams.helpers.show-trainee-details :exam="$exam" :type="$type" />
        </div>
    </div>

    <div class="mb-4 border border-nlrc-blue-100 dark:border-nlrc-blue-900 rounded flex flex-col">
        <div class="p-4 flex flex-row border-b border-nlrc-blue-50 bg-nlrc-blue-50 dark:border-nlrc-blue-900 dark:bg-nlrc-blue-900">
            <p class="pe-4 text-nlrc-blue-500 dark:text-slate-400 font-black text-lg grow-0">3.</p>
            <div class="w-full">
                <p>Add the result of the new attempt.</p>
            </div>
        </div>

        <div class="p-4 ms-8 dark:bg-nlrc-blue-800">
            <livewire:exams.helpers.assign-test-grade :exam="$exam" :type="$type" />
        </div>
    </div>
</x-page-section>

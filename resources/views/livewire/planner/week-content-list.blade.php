<div class='w-72 p-2 flex'>

    <div class='shrink-0 grow mb-2'>

        @if ($this->contentType->value === 'brush-up_week')
            <p class='font-bold text-base'>Brush-up week</p>
        @endif

        @if ($this->contentType->value === 'none')
            <p><span class="font-bold text-base">None</span> (end of curriculum)</p>
        @endif

        @if (count($unitNames) > 0)
            <div class='font-bold text-base'>
                {{ implode(', ', $unitNames) }}
            </div>
        @endif

        @if (count($meetingNames) > 0)
            <div>
                {{ implode(', ', $meetingNames) }}
            </div>
        @endif

        @if (($this->customContent && $this->showCustomContent) || $this->contentType->value === 'custom_content')
            <p class='max-w-52 font-bold text-base line-clamp-1 text-ellipsis overflow-hidden nlrc markdown'>{!! $this->customContent !!}</p>
        @endif
    </div>

    <div class="grow-0 h-fit max-w-16 grid {{ $finalized ? 'grid-cols-1' : 'grid-cols-2' }} gap-2" x-data="{ isEditing: false, activeTraineeCount: @entangle('activeTraineeCount'), originalTraineeCount: @entangle('activeTraineeCount') }" @keydown.escape.window="isEditing = false">
       
        <div class='text-center text-base' :class="{ 'italic': !{{ $this->finalized }}, 'font-bold': {{ $this->finalized }} }" title="Number of trainees in this group">
            <template x-if="!isEditing">
                <span @click="if ({{ $this->finalized }}) isEditing = true">
                    {{ number_format($activeTraineeCount, 0) }}
                </span>
            </template>
            <template x-if="isEditing">
                <div class='grid grid-cols-2 gap-1'>
                    <x-input type="number" x-model="activeTraineeCount" step="1" @keydown.enter.="isEditing = false; $wire.saveTraineeCount()" class="text-center col-span-full text-xs p-1" />
                    <button @click="isEditing = false; $wire.saveTraineeCount(); $dispatch('countUpdated')" class="rounded p-1 border border-nlrc-blue-200 dark:border-nlrc-blue-600 bg-nlrc-blue-100 dark:bg-nlrc-blue-800 hover:bg-nlrc-blue-200 dark:hover:bg-nlrc-blue-700">
                        <x-heroicon-o-check class="w-3 h-3" />
                    </button>
                    <button @click="isEditing = false; activeTraineeCount = originalTraineeCount" class="rounded p-1 border border-nlrc-blue-200 dark:border-nlrc-blue-600 bg-nlrc-blue-100 dark:bg-nlrc-blue-800 hover:bg-nlrc-blue-200 dark:hover:bg-nlrc-blue-700">
                        <x-heroicon-o-x-mark class="w-3 h-3" />
                    </button>
                </div>
            </template>
        </div>

        @if (!$finalized)
            <x-filament::button wire:click="extendSchedule" color="gray" size="xs" title="Extend this group's schedule by one week (does not overwrite anything)">
                <x-heroicon-o-forward class="h-4 w-auto"/>
            </x-filament::button>

            <x-filament::button color="gray" size="xs" title="Edit" wire:click="deleteThisWeek">
                <x-heroicon-o-trash class="h-4 w-auto"/>
            </x-filament::button>

            <x-filament::button color="gray" size="xs" title="Edit" wire:click="openForm">
                <x-heroicon-o-pencil class="h-4 w-auto"/>
            </x-filament::button>

        @endif
    </div>

</div>
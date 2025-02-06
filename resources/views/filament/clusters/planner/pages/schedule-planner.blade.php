<x-filament-panels::page>

{{-- Date range selection --}}
    <div class="flex items-end mb-4 gap-2">
        <div>
            <x-label for="start_date">Start date</x-label>
            <x-input type="date" id="start_date" wire:model="startDate"/>
        </div>
        <div>
            <x-label for="end_date">End date</x-label>
            <x-input type="date" id="end_date" wire:model="endDate"/>
        </div>
        <x-button wire:click="$refresh" class="mb-1" title="Update the view with the selected dates">
            <x-heroicon-o-arrow-path class="h-4 w-auto"/>
        </x-button>
    </div>

    {{-- Schedule table --}}
    <div class="overflow-auto text-sm relative">
        <table class="table-fixed">
            <thead>
                <tr>
                    {{-- Headers --}}
                    <th class='sticky left-0 z-10 bg-gray-50 dark:bg-gray-950 align-bottom'>
                        <p class='font-normal mb-2'>sorting</p>
                        <x-filament::button wire:click="toggleSortByGroupName" title="Sort by ascending or descending group name or group id (age)" color="gray">
                            @switch ($this->sortByGroupNameState)
                                @case(0)
                                    ID
                                    @break
                                @case(1)
                                    <x-heroicon-o-chevron-down class="h-4 w-auto"/>
                                    @break
                                @case(2)
                                    <x-heroicon-o-chevron-up class="h-4 w-auto"/>
                                    @break
                            @endswitch

                        </x-filament::button>
                    </th>
                    @foreach ($this->weeks as $week)
                        <th class='w-52 border-s {{ $week->finalized ? 'bg-green-200 dark:bg-green-950 border-green-400 dark:border-green-800' : 'bg-yellow-100 dark:bg-yellow-950 border-yellow-300 dark:border-yellow-900' }}' key="week-{{ $week->id }}">
                            <div class="flex flex-col">
                                <h3 class="ps-2 pt-2 text-left">Week {{ $week->number }}<br><span class="font-normal">{{ $this->niceWeekDates($week) }}</span></h3>

                                <div class='px-2 pt-1 flex gap-2 justify-between'>
                                    <x-filament::button wire:click="sortByWeek({{ $week->id }})" color="{{ $week->id === $this->sortByWeekId ? 'success' : 'gray' }}" title="Sort by this week's unit sort values">
                                        <x-heroicon-o-chevron-down class="h-4 w-auto"/>
                                    </x-filament::button>

                                    @if ($this->isNotInThePast($week) && auth()->user()->hasAnyRole(['Admin', 'Manager']))
                                        <div class="flex gap-2 justify-end">
                                            @if (!$week->finalized)
                                                <x-danger-button wire:click="confirmDelete({{ $week->id }})" title='Delete all schedules for this week'>
                                                    <x-heroicon-o-trash class="h-4 w-auto"/>
                                                </x-danger-button>
                                            @endif
    
                                            <x-button wire:click="finalizeWeek({{ $week->id }})" class="{{ $week->finalized ? 'bg-nlrc-green-100 dark:bg-nlrc-green-200 focus:bg-nlrc-green-100 active:bg-nlrc-green-100 hover:bg-nlrc-green-200 dark:hover:bg-nlrc-green-100 dark:focus:bg-nlrc-green-200 dark:active:bg-nlrc-green-200' : 'bg-yellow-500 dark:bg-yellow-800 focus:bg-yellow-400 active:bg-yellow-400 hover:bg-yellow-400 dark:hover:bg-yellow-700 dark:focus:bg-yellow-700 dark:active:bg-yellow-700' }}" title="Mark the schedule as {{ $week->finalized ? 'tentative (allows editing but reverts trainee count to dynamic, overwriting any manual count changes)' : 'final (prevents editing)' }}">
                                                @if ($week->finalized)
                                                    <x-heroicon-o-lock-closed class="h-4 w-auto"/>
                                                @else
                                                    <x-heroicon-o-lock-open class="h-4 w-auto"/>
                                                @endif
                                            </x-button>
    
                                            <x-button wire:click="extendSchedules({{ $week->id }})" title='Extend all schedules by one week'>
                                                <x-heroicon-o-forward class="h-4 w-auto"/>
                                            </x-button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr class='bg-gray-200 dark:bg-gray-800'>
                    <td class='px-4 py-1 sticky left-0 z-10 bg-gray-50 dark:bg-gray-950 font-bold'></td>
                    @foreach ($this->weeks as $week)
                        <td class='border-s {{ $week->finalized ? 'bg-green-200 dark:bg-green-950 border-green-400 dark:border-green-800' : 'bg-yellow-100 dark:bg-yellow-950 border-yellow-300 dark:border-yellow-900' }} text-right text-base font-bold pb-1 px-2'>
                            {{ $this->weeklyTraineeCounts[$week->id] ?? 0 }}
                        </td>
                    @endforeach
                </tr>
                @foreach ($this->groups as $group)
                    <tr class='odd:bg-gray-100 dark:odd:bg-gray-900' key="group-{{ $group->id }}">
                        <td class='p-4 sticky left-0 z-10 bg-gray-200 dark:bg-gray-800 border-b border-gray-50 dark:border-gray-900'>
                            <a href="{{ url('/admin/trainees?tableFilters[group_id][value]=' . $group->id . '&tableFilters[active][value]=1') }}" class="hover:text-gray-500 dark:hover:text-gray-400">
                                {{ $group->group_code }}
                            </a>
                        </td>
                        @foreach ($this->weeks as $week)

                                <td class='border-s border-gray-300 dark:border-gray-800 align-top'>

                                    @php
                                        $schedule = $this->getScheduleForGroupAndWeek($group->id, $week->id);
                                        $filteredData = $this->filteredUnitsAndMeetings($group->id, $week->id);
                                        // dd($filteredData);
                                    @endphp

                                    {{-- :key with timestamp forces a re-render when the parent updates, i.e. when date ranges are changed --}}
                                    <livewire:planner.week-content-list 
                                        :groupId="$group->id" 
                                        :weekId="$week->id"
                                        :finalized="$week->finalized"
                                        :unitNames="$filteredData['units']"
                                        :meetingNames="$filteredData['meetings']"
                                        :schedule="$schedule"
                                        :key="'group-'.$group->id.'-week-'.$week->id.'-'.now()->timestamp"
                                    />

                                </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-filament::modal id="deleteConfirmationModal">
        <x-slot name="heading">
            Confirm deletion
        </x-slot>

        <x-slot name="description">
            This will delete all schedules for this week. Do you want to continue?
        </x-slot>

        <div class='flex gap-2 w-full'>
            <x-filament::button wire:click="deleteWeek" color="danger">
                Yes, delete
            </x-filament::button>

            <x-filament::button wire:click="$dispatch('close-modal', {id: 'deleteConfirmationModal'})" color="gray">
                Cancel
            </x-filament::button>
        </div>
    </x-filament::modal>

    <livewire:planner.week-content-form />
    <livewire:planner.delete-week-modal />

</x-filament-panels::page>
@php
    use \App\Enums\Planner\ContentType;
@endphp

<div class="flex flex-col gap-4">
    <div class='flex gap-2'>
        <x-heroicon-s-calendar-days class='h-6 w-auto' />
        @if ($groupName === 'No group')
            <h2 class="text-lg font-medium">You have no active group at the moment.</h2>
        @else
            <h2 class="text-lg font-medium">Schedule for {{ $groupName }}</h2>
        @endif
    </div>

    <div class='border border-nlrc-blue-200 dark:border-nlrc-blue-900 rounded'>
        <x-planner.weekly-schedule 
            :weeklySchedule="$currentWeeklySchedule" 
            :units="$currentUnits" 
            :meetings="$currentMeetings"
            :dateRange="$currentDateRange"
            title="This week" 
        />

        <x-planner.weekly-schedule 
            :weeklySchedule="$nextWeeklySchedule" 
            :units="$nextUnits" 
            :meetings="$nextMeetings" 
            :dateRange="$nextDateRange"
            title="Next week" 
        />

        <x-planner.weekly-schedule 
            :weeklySchedule="$twoWeeksLaterWeeklySchedule" 
            :units="$twoWeeksLaterUnits" 
            :meetings="$twoWeeksLaterMeetings" 
            :dateRange="$twoWeeksLaterDateRange"
            title="Two weeks from now"
        />
    </div>
</div>
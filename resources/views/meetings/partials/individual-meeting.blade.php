@php
    // Doing this directly with $trainee->pivot->instructor->name would create a db query for each meeting, so we don't want that
    $meetingStatusId = $trainee->pivot->meeting_status_id;
    $meetingStatusName = $trainee->meetingTrainees->firstWhere('meeting_status_id', $meetingStatusId);

    $instructorId = $trainee->pivot->instructor_id;
    $instructor = $trainee->meetingTrainees->firstWhere('instructor_id', $instructorId);

    $colorClasses = $meetingStatusId == 1 ? 'bg-green-50 border-green-200 dark:bg-green-800 dark:border-green-900' : ($meetingStatusId == 2 ? 'bg-red-50 border-red-200 dark:bg-red-800 dark:border-red-900' : 'bg-nlrc-blue-50 border-nlrc-blue-200 dark:bg-nlrc-blue-600 dark:border-nlrc-blue-700');
    $headerColors = $meetingStatusId == 1 ? 'bg-green-100 border-green-200 dark:bg-green-900 dark:border-green-900' : ($meetingStatusId == 2 ? 'bg-red-100 border-red-200 dark:bg-red-900 dark:border-red-900' : 'bg-nlrc-blue-100 border-nlrc-blue-200 dark:bg-nlrc-blue-700 dark:border-nlrc-blue-700');
    $textColorClass = $meetingStatusId == 1 ? 'text-green-700 dark:text-green-400' : ($meetingStatusId == 2 ? 'text-red-700 dark:text-red-400' : 'text-slate-700 dark:text-slate-200');

@endphp

<li class="border text-sm rounded flex flex-col border-b {{ $headerColors }}">
    <div class="p-1 sm:p-2 py-2 flex flex-row justify-between">
        <p class="font-bold">Attempt #{{ $loop->iteration }}</p>
        <p class="text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($trainee->pivot->date)->format('M j, Y') }}</p>
    </div>

    <div class="grow {{ $colorClasses }} flex flex-col">
        <div class="grid grid-cols-3 sm:grid-cols-4 border-b {{ $colorClasses }}">
            <p class="text-slate-700 dark:text-slate-200 p-1 sm:p-2">Unit status:</p>
            <div class="col-span-2 sm:col-span-3 p-1 sm:p-2">
                @if ($meetingStatusId == 1)
                    {{-- Solid check-circle from Heroicons --}}
                    <svg class="inline-block h-4 text-nlrc-green-200 dark:text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                    </svg>
                @else
                    {{-- Solid x-circle from heroicons --}}
                    <svg class="inline-block h-4 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                    </svg>
                @endif
                <span class="font-bold {{ $textColorClass }}">{{ $meetingStatusName->meetingStatus->name }}</span>
            </div>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 border-b {{ $colorClasses }}">
            <p class="text-slate-700 dark:text-slate-200  p-1 sm:p-2">Feedback:</p>
            <div class="col-span-2 sm:col-span-3 p-1 sm:p-2">{{ $trainee->pivot->feedback }}</div>
        </div>

        @if ($instructor)
        <div class="grid grid-cols-3 sm:grid-cols-4">
            <p class="text-slate-700 dark:text-slate-200  p-1 sm:p-2">Instructor:</p>
            <div class="col-span-2 sm:col-span-3 p-1 sm:p-2">{{ $instructor->instructor->name }}</div>   
        </div>
        @endif

    </div>

</li>
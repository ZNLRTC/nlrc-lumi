<div class="col-span-2">
    @if ($currentTraineeId)
        <p>Latest meetings of the selected trainee:</p>
    @endif
    <div class="grid grid-cols-1 text-sm mt-4">
        @foreach ($latestMeetings as $meeting)

            @php
                $meetingStatus = $meeting->pivot->meetingStatus->name;
                $colorClasses = $meetingStatus == "Completed" ? 'bg-green-50 border-green-200 dark:bg-nlrc-blue-800 dark:border-green-900' : ($meetingStatus == "Incomplete" ? 'bg-red-50 border-red-200 dark:bg-nlrc-blue-800 dark:border-red-900' : 'bg-nlrc-blue-50 border-nlrc-blue-200 dark:bg-nlrc-blue-800 dark:border-nlrc-blue-900');
                $headerColorClasses = $meetingStatus == "Completed" ? 'bg-green-100 border-green-200 dark:bg-green-900 dark:border-green-900' : ($meetingStatus == "Incomplete" ? 'bg-red-100 border-red-200 dark:bg-red-900 dark:border-red-900' : 'bg-nlrc-blue-100 border-nlrc-blue-200 dark:bg-nlrc-blue-900 dark:border-nlrc-blue-900');
                $textColorClass = $meetingStatus == "Completed" ? 'text-green-700 dark:text-green-400' : ($meetingStatus == "Incomplete" ? 'text-red-700 dark:text-red-400' : 'text-slate-700 dark:text-slate-200');
            @endphp

            @if ($meeting && $meeting->pivot)
                <div class="mb-4 border {{ $colorClasses }} rounded flex flex-col">
                    <div class="px-4 py-3 border-b {{ $headerColorClasses }} flex flex-row">
                        <div class="grow">
                            <p><span class="font-bold">{{ $meeting->unit->name }} meeting</span> on {{ \Carbon\Carbon::parse($meeting->pivot->date)->inUserTimezone()->format('D, M j, Y') }}</p>
                            <p><span>
                                @if ($meetingStatus == "Completed")
                                    <x-heroicon-s-check-circle class="h-4 text-nlrc-green-200 dark:text-green-600 inline-block" />
                                @else
                                    <x-heroicon-s-x-circle class="inline-block h-4 text-red-600" /> 
                                @endif
                            </span> <span class="font-bold {{ $textColorClass }}">{{ $meetingStatus }}</span></p>
                        </div>

                        <div class="grow-0">
                            {{-- Only allow instructors to delete within two days of the creation of the meeting --}}
                            @if (($meeting->pivot->instructor_id == auth()->id() && $meeting->pivot->created_at->gt(Carbon\Carbon::now()->subDays(2))) || auth()->user()->hasAnyRole(['Admin','Manager','Staff']))
                                <x-danger-button title="Delete" wire:click="deleteMeeting({{ $meeting->pivot->id }})" wire:confirm='Delete this meeting?'>
                                    <x-heroicon-o-trash class="h-4"/>                                  
                                  </x-button>
                            @endif
                        </div>
                    </div>
                    <div class="px-4 py-3">
                        <table>
                            <tr>
                                <td class="pe-2 align-top text-slate-700 dark:text-slate-400">Instructor:
                                <td>{{ $meeting->pivot->instructor->name }}</td>
                            </tr>
                            <tr>
                                <td class="pe-2 align-top text-slate-700 dark:text-slate-400">Feedback:</td>
                                <td class="italic">{{ $meeting->pivot->feedback }}</td>
                            </tr>
                            <tr>
                                <td class="pe-2 align-top text-slate-700 dark:text-slate-400">Notes:</td>
                                <td>{{ $meeting->pivot->internal_notes }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        <button wire:click="loadMore" class="flex justify-center items-center gap-2 {{ $allMeetingsLoaded || !$currentTraineeId ? 'hidden' : '' }}">
            <span>Load more</span>

            <x-loading-indicator size='5' :showText='false' target="loadMore" />
 
        </button>
    </div>
</div>

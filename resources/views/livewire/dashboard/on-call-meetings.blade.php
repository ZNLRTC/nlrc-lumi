<div x-data="{openConfirmCancelMeetingModal(meetingOnCallId) {
            {{-- Set $meeting_on_call_id here --}}
            $dispatch('on-call-meeting-id-set', [meetingOnCallId]);
        }
    }"
>
    <h2 class="flex items-center gap-2 mb-2 text-xl dark:text-white">
        <x-heroicon-o-video-camera class="h-6 w-auto" />
        <span>On-call meetings</span>
    </h2>

    @if (Auth::user()->hasRole('Instructor'))
        <a wire:click="set_form_type('add')" class="my-2 inline-block text-nlrc-blue-500 dark:text-sky-500 hover:text-nlrc-blue-600 dark:hover:text-sky-400 hover:cursor-pointer">Set a new on-call meeting</a>

        <p class="my-2">Don't forget to complete your meetings here once you're done.</p>

        <x-action-message class="me-3" on="on-call-meeting-processed">
            <div class="text-lg px-4 py-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-400">
                {{ $form_type == 'add' ? 'Successfully added a new on-call meeting!' : 'Successfully changed on-call meeting details' }}
            </div>
        </x-action-message>

        <x-action-message class="me-3" on="on-call-meeting-cancelled">
            <div class="text-lg px-4 py-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-400">You have cancelled an on-call meeting</div>
        </x-action-message>
    @else
        <span class="my-2 inline-block">All times are shown in the timezone you set in your user profile. You need to join at least 5 minutes before the on-call meeting starts. You may opt-in/opt-out of notifications by clicking the bell icon of the respective meeting</span>

        <x-action-message class="me-3" on="on-call-meeting-opted-in">
            <div class="text-lg px-4 py-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-400">You will receive a notification about this on-call meeting</div>
        </x-action-message>

        <x-action-message class="me-3" on="on-call-meeting-opted-out">
            <div class="text-lg px-4 py-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-400">You will NOT receive a notification about this on-call meeting</div>
        </x-action-message>
    @endif

    @isset ($next_meeting_on_call)
        <div class="mt-4 mb-6 border border-nlrc-blue-200 dark:border-nlrc-blue-600 dark:bg-nlrc-blue-800">
            {{-- TODO: We can probably use a cronjob or something to check for current time then automatically update the status to on-going --}}
            @if (\Carbon\Carbon::now(Auth::user()->timezone)->between(
                    \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($next_meeting_on_call['start_time'], 'Y-m-d H:i:s'),
                    \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($next_meeting_on_call['end_time'], 'Y-m-d H:i:s')
                )
            )
                <h2 class="text-lg p-2 flex items-center justify-between bg-nlrc-blue-200 dark:bg-nlrc-blue-600 dark:text-white">
                    <span>On-going</span>

                    <span class="relative flex h-4 w-4">
                        <span class="absolute inline-flex rounded-full h-full w-full opacity-50 animate-ping bg-nlrc-blue-500 dark:bg-white"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                    </span>
                </h2>
            @else
                <h2 class="text-lg p-2 bg-nlrc-blue-200 dark:bg-nlrc-blue-600 dark:text-white">Next</h2>
            @endif

            {{-- Show the button at least 15 minutes before end time --}}
            @if (Auth::user()->hasRole('Instructor') && \Carbon\Carbon::now(Auth::user()->timezone) >= \Carbon\Carbon::parse($next_meeting_on_call['end_time'], 'UTC')->setTimezone(Auth::user()->timezone)->subMinutes(15) && Auth::user()->id == $next_meeting_on_call['user_id'])
                <div class="p-2 mt-2">
                    <x-secondary-button wire:click="$toggle('confirm_complete_meeting_modal')" wire.loading.attr="disabled">Complete Meeting</x-secondary-button>
                </div>
            @endif

            <div class="p-2">
                @php
                    $meeting_date = \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($next_meeting_on_call['start_time'], 'M j, Y');
                    $start_time = \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($next_meeting_on_call['start_time'], 'g:i A');
                    $end_time = \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($next_meeting_on_call['end_time'], 'g:i A');
                @endphp

                <h3>{{ $meeting_date }}</h3>
                <p class="text-sm">{{ $start_time }} ~ {{ $end_time }}</p>
                <p class="text-sm">Created by: <span>{{ $next_meeting_on_call['user']['name'] }}</span></p>

                {{-- Hide the button at least 10 minutes before end time for instructors and show it 5 minutes before start time for trainees --}}
                @if (
                    (Auth::user()->hasRole('Instructor') && \Carbon\Carbon::now(Auth::user()->timezone) <= \Carbon\Carbon::parse($next_meeting_on_call['end_time'], 'UTC')->setTimezone(Auth::user()->timezone)->subMinutes(10)) ||
                    (Auth::user()->hasRole('Trainee') && \Carbon\Carbon::now(Auth::user()->timezone) >= \Carbon\Carbon::parse($next_meeting_on_call['start_time'], 'UTC')->setTimezone(Auth::user()->timezone)->subMinutes(5))
                )
                    <a class="break-all text-sm text-nlrc-blue-500 dark:text-sky-500 hover:text-nlrc-blue-600 dark:hover:text-sky-400" href="{{ $next_meeting_on_call['meeting_link'] }}" target="_blank">{{ $next_meeting_on_call['meeting_link'] }}</a>
                @endif
            </div>
        </div>
    @endisset

    <div class="mt-4 mb-6 border border-nlrc-blue-200 dark:border-nlrc-blue-600 dark:bg-nlrc-blue-800 rounded">
        @if (count($meetings_on_calls) > 0)
            <h2 class="text-lg p-2 bg-nlrc-blue-200 dark:bg-nlrc-blue-600 dark:text-white">Upcoming</h2>
        @endif

        <div class="space-y-4 px-2 py-4">
            @forelse ($meetings_on_calls as $meeting_on_call)
                @php
                    $is_meeting_on_call_cancelled = $meeting_on_call['meeting_status'] == \App\Enums\MeetingsOnCallsMeetingStatus::CANCELLED;

                    $meeting_date = \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['start_time'], 'M j, Y');
                    $start_time = \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['start_time'], 'g:i A');
                    $end_time = \App\Models\Meetings\MeetingsOnCall::parse_utc_timestamp_to_user_timezone($meeting_on_call['end_time'], 'g:i A');
                @endphp

                <div class="flex justify-between gap-2">
                    <div class="flex flex-col {{ $is_meeting_on_call_cancelled ? 'line-through text-slate-300 dark:text-slate-600' : '' }}">
                        <span class="text-base">{{ $meeting_date }}</span>
                        <p class="text-sm">{{ $start_time }} ~ {{ $end_time }}</p>
                        <p class="text-sm">Created by: <span>{{ $meeting_on_call['user']['name'] }}</span></p>

                        {{--
                        @if ($meeting_on_call['meeting_status'] != \App\Enums\MeetingsOnCallsMeetingStatus::CANCELLED)
                            <a class="break-all text-sm text-nlrc-blue-500 dark:text-sky-500 hover:text-nlrc-blue-600 dark:hover:text-sky-400 " href="{{ $meeting_on_call['meeting_link'] }}" target="_blank">{{ $meeting_on_call['meeting_link'] }}</a>
                        @endif
                        --}}
                    </div>

                    {{-- Can only cancel 30 minutes or later before the meeting starts --}}
                    @if (Auth::user()->hasRole('Instructor') && Auth::user()->id == $meeting_on_call['user_id'] && \Carbon\Carbon::now(Auth::user()->timezone) <= \Carbon\Carbon::parse($meeting_on_call['start_time'], 'UTC')->setTimezone(Auth::user()->timezone)->subMinutes(30))
                        <div class="flex justify-between items-start gap-2">
                            @if ($meeting_on_call['meeting_status'] == \App\Enums\MeetingsOnCallsMeetingStatus::PENDING)
                                <x-secondary-button wire:click="set_form_type('edit', {{ $meeting_on_call['id'] }})" class="flex items-center gap-1.5 bg-orange-300 dark:bg-orange-700 dark:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                    <span>Edit</span>
                                </x-secondary-button>

                                {{-- TODO: If the instructor cancels an on-call meeting, broadcast an event to re-render this component so that logged in users will see that the event is cancelled. Only send it to trainees who has their notifications turned on --}}
                                <x-danger-button x-on:click="openConfirmCancelMeetingModal({{ $meeting_on_call['id'] }})" class="gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                    </svg>
                                    <span>Cancel</span>
                                </x-danger-button>
                            @endif
                        </div>
                    @elseif (Auth::user()->hasRole('Trainee'))
                        @if (!$is_meeting_on_call_cancelled)
                            @php
                                $has_opt_in = $meeting_on_call->meetings_on_calls_opt_ins->first();
                            @endphp

                            <div>
                                <svg wire:click="set_opt_in_notification({{ $meeting_on_call['id'] }})" class="size-6 text-blue-600 dark:text-blue-300 hover:cursor-pointer {{ $has_opt_in && $has_opt_in->is_opt_in == 1 ? 'fill-blue-300 dark:fill-blue-600' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"></path>
                                    <title>{{ $has_opt_in && $has_opt_in->is_opt_in == 1 ? 'Opt-out' : 'Opt-in' }} for this on-call meeting</title>
                                </svg>
                            </div>
                        @else
                            <p>This meeting is <span class="font-bold line-through text-red-500">cancelled</span></p>
                        @endif
                    @endif
                </div>
            @empty
                <p>No upcoming on-call meetings scheduled.</p>
            @endforelse
        </div>
    </div>

    <div class="p-2 bg-nlrc-blue-100 dark:bg-nlrc-blue-900 border border-nlrc-blue-200 dark:border-nlrc-blue-900 rounded text-sm">
        <div class="flex gap-2 items-center mb-2">
            <x-heroicon-o-question-mark-circle class='h-6 text-slate-600 dark:text-slate-300'/>
            <h2 class="font-bold">What are on-call meetings?</h2>
        </div>

        <p class="text-justify text-sm text-slate-500 dark:text-slate-400">On-call meetings are free-for-all meetings where anyone can hop in to chat with the instructor in or about Finnish. You cannot complete benchmark tasks during these but can chat with others in Finnish or ask for help.</p>
    </div>

    @if (Auth::user()->hasRole('Instructor'))
        <x-modal wire:model="meeting_on_call_form_modal">
            <div class="flex justify-between px-6 py-2 border-b border-b-slate-600 text-xl">
                <h2 class="dark:text-white">{{ ucfirst($form_type) }} on-call meeting</h2>

                <button wire:click="$toggle('meeting_on_call_form_modal')" class="dark:text-white">&times;</button>
            </div>

            <div class="px-6 py-4">
                <x-form-section :columns_on_breakpoint_md="1" :submit="'process_meeting_on_call_form'">
                    <x-slot name="description">You can only have a maximum of 2 hours per meeting, and a minimum of 30 minutes per meeting. Cancelled meetings can be re-created. All times you set in here will reflect in the trainees' respective timezones.</x-slot>

                    <x-slot name="form">
                        <div class="col-span-12">
                            <x-label value="{{ __('Meeting Link') }}" is_required="true" for="meeting_link" />

                            <x-input wire:model="meeting_link" class="mt-1 block w-full placeholder-slate-800 focus:placeholder-slate-400 dark:placeholder-slate-400 dark:focus:placeholder-slate-200" type="text" id="meeting_link" autocomplete="meeting_link" />
                            <small class="text-slate-700 dark:text-slate-300">Must start with https://meet.google.com/</small>

                            <x-input-error class="mt-2" for="meeting_link" />
                        </div>

                        <div class="col-span-12 px-2 py-4 grid grid-cols-1 border border-nlrc-blue-400 sm:grid-cols-2">
                            <div class="mb-4 sm:col-span-2">
                                <x-label value="{{ __('Meeting Date (start)') }}" is_required="true" for="start_time_meeting_date" />

                                <x-input wire:model.live="start_time_meeting_date" class="mt-1 block w-full" type="date" id="start_time_meeting_date" autocomplete="start_time_meeting_date" />
                                <small class="text-slate-700 dark:text-slate-300">Format: DD/MM/YYYY</small>

                                <x-input-error class="mt-2" for="start_time_meeting_date" />
                            </div>

                            <div class="md:me-2">
                                <x-label value="{{ __('Start Time') }}" is_required="true" for="start_time" />

                                <x-select wire:model.live="start_time" :inline_block="false" class="w-full" id="start_time">
                                    <option value="">Select start time</option>
                                    @foreach ($times as $start_time)
                                        <option value="{{ $start_time }}">{{ $start_time }}</option>
                                    @endforeach
                                </x-select>

                                <x-input-error class="mt-2" for="start_time" />
                            </div>

                            <div>
                                <x-label value="{{ __('AM / PM') }}" is_required="true" for="start_time_am_pm" />

                                <x-select wire:model.live="start_time_am_pm" :inline_block="false" class="w-full" id="start_time_am_pm">
                                    <option value="">Select AM / PM</option>
                                    @foreach ($am_pms as $am_pm)
                                        <option value="{{ $am_pm }}">{{ strtoupper($am_pm) }}</option>
                                    @endforeach
                                </x-select>

                                <x-input-error class="mt-2" for="start_time_am_pm" />
                            </div>
                        </div>

                        <div class="col-span-12 px-2 py-4 grid grid-cols-1 border border-nlrc-blue-400 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-label value="{{ __('Meeting Date (end)') }}" is_required="true" for="end_time_meeting_date" />

                                <x-input wire:model.live="end_time_meeting_date" wire:target="toggle_same_start_time_and_end_time" wire:loading.attr="disabled" class="mt-1 block w-full" type="date" id="end_time_meeting_date" autocomplete="end_time_meeting_date" />
                                <small class="text-slate-700 dark:text-slate-300">Format: DD/MM/YYYY</small>

                                <x-input-error class="mt-2" for="end_time_meeting_date" />
                            </div>

                            <div class="mb-4 sm:col-span-2">
                                <x-checkbox wire:model="is_same_start_time_and_end_time" wire:click="toggle_same_start_time_and_end_time" wire:target="toggle_same_start_time_and_end_time" wire:loading.attr="disabled" /> <span class="dark:text-slate-300">Same day</span>

                                @if (!$is_same_start_time_and_end_time)
                                    <x-loading-indicator
                                        :loader_color_bg="'fill-slate-900 dark:fill-white'"
                                        :loader_color_spin="'fill-slate-900 dark:fill-white'"
                                        :showText="false"
                                        :size="4"
                                        :target="'toggle_same_start_time_and_end_time'"
                                    />
                                @endif
                            </div>

                            <div class="md:me-2">
                                <x-label value="{{ __('End Time') }}" is_required="true" for="end_time" />

                                <x-select wire:model.live="end_time" :inline_block="false" class="w-full" id="end_time">
                                    <option value="">Select end time</option>
                                    @foreach ($times as $end_time)
                                        <option value="{{ $end_time }}">{{ $end_time }}</option>
                                    @endforeach
                                </x-select>

                                <x-input-error class="mt-2" for="end_time" />
                            </div>

                            <div>
                                <x-label value="{{ __('AM / PM') }}" is_required="true" for="end_time_am_pm" />

                                <x-select wire:model.live="end_time_am_pm" :inline_block="false" class="w-full" id="end_time_am_pm">
                                    <option value="">Select AM / PM</option>
                                    @foreach ($am_pms as $am_pm_end)
                                        <option value="{{ $am_pm_end }}">{{ strtoupper($am_pm_end) }}</option>
                                    @endforeach
                                </x-select>

                                <x-input-error class="mt-2" for="end_time_am_pm" />
                            </div>
                        </div>

                        <div class="col-span-12">
                            <x-button wire.loading.attr="disabled">
                                <span wire:loading.flex wire:target="process_meeting_on_call_form">
                                    <x-loading-indicator
                                        :loader_color_bg="'fill-white'"
                                        :loader_color_spin="'fill-white'"
                                        :showText="false"
                                        :size="4"
                                    />

                                    <span class="ml-2">Submitting</span>
                                </span>
                                <span wire:loading.remove wire:target="process_meeting_on_call_form">Submit</span>
                            </x-button>
                        </div>
                    </x-slot>
                </x-form-section>
            </div>
        </x-modal>

        <x-confirmation-modal wire:model="confirm_complete_meeting_modal">
            <x-slot name="title">Complete meeting?</x-slot>

            <x-slot name="content">Are you sure you want to complete the meeting? This will mark the on-call meeting as completed.</x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirm_complete_meeting_modal')" wire:loading.attr="disabled">No</x-secondary-button>

                <x-button wire.loading.attr="disabled" class="ml-4">
                    <span wire:loading.flex wire:target="complete_meeting_on_call">
                        <x-loading-indicator
                            :loader_color_bg="'fill-white'"
                            :loader_color_spin="'fill-white'"
                            :showText="false"
                            :size="4"
                        />

                        <span class="ml-2">Completing</span>
                    </span>
                    <span wire:loading.remove wire:target="complete_meeting_on_call" wire:click="complete_meeting_on_call">Confirm Complete</span>
                </x-button>
            </x-slot>
        </x-confirmation-modal>

        <x-confirmation-modal wire:model="confirm_cancel_meeting_modal">
            <x-slot name="title">Cancel meeting?</x-slot>

            <x-slot name="content">Are you sure you want to cancel the meeting? This will mark the on-call meeting as cancelled to everyone.</x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirm_cancel_meeting_modal')" wire:loading.attr="disabled">No</x-secondary-button>

                <x-button wire.loading.attr="disabled" class="ml-4">
                    <span wire:loading.flex wire:target="cancel_meeting_on_call">
                        <x-loading-indicator
                            :loader_color_bg="'fill-white'"
                            :loader_color_spin="'fill-white'"
                            :showText="false"
                            :size="4"
                        />

                        <span class="ml-2">Cancelling</span>
                    </span>
                    <span wire:loading.remove wire:target="cancel_meeting_on_call" wire:click="cancel_meeting_on_call({{ $meeting_on_call_id }})">Confirm Cancel</span>
                </x-button>
            </x-slot>
        </x-confirmation-modal>
    @endif
</div>

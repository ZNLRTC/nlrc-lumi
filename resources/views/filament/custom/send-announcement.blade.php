<div class="grid items-center grid-cols-1 sm:grid-cols-2">
    <x-label value="Send this announcement to" for="active_choice" />

    <x-filament::input.wrapper>
        <x-filament::input.select wire:model="active_choice" wire:change="change_active_choice" id="active_choice">
            <option value="groups">Active Groups w/ Active Trainees</option>
            <option value="trainees">Trainees</option>
        </x-filament::input.select>
    </x-filament::input.wrapper>

    <div wire:loading.grid wire:target="change_active_choice" class="my-4 mx-auto col-span-2">
        <x-filament::loading-indicator class="h-20 w-20" /> Loading...
    </div>

    <div wire:loading.remove wire:target="change_active_choice" class="col-span-1 sm:col-span-2">
        <div class="pt-4">
            <x-action-message :font_size="'lg'" :type="'success'" on="send-announcement-success">Announcement successfully sent to {{ $inserted_records_count }} 
                @if ($active_choice == 'groups')
                    {{ $inserted_records_count > 1 ? 'trainees' : 'trainee '}} in {{ $group_recipients_count }}
                    {{ $group_recipients_count > 1 ? $active_choice : substr($active_choice, 0, -1) }}
                @elseif ($active_choice == 'trainees')
                    {{ $inserted_records_count > 1 ? $active_choice : substr($active_choice, 0, -1) }}!
                @endif
            </x-action-message>
            <x-action-message :font_size="'lg'" :type="'danger'" on="send-announcement-error">Announcement not successfully sent as the recipient(s) have already received the announcement.</x-action-message>

            @if ($active_choice == 'groups')
                @if ($groups_recipients)
                    <div class="border mb-4 p-4">
                        <h2>Announcement will be sent to {{ count($groups_recipients) }} {{ count($groups_recipients) > 1 ? 'groups' : 'group' }}:</h2>
                        <ul class="flex flex-wrap gap-2">
                            @foreach ($groups_recipients as $key => $group)
                                <li class="flex gap-2 border-2 border-solid border-green-500 rounded-xl py-1 px-2 w-fit">
                                    <span>{{ $group }}</span>
                                    <button wire:click.prevent="remove_recipient({{ $key }})" wire:loading.remove wire:target="remove_recipient({{ $key }})" title="Remove recipient">&times;</button>
                                    <div wire:loading.inline-flex wire:target="remove_recipient({{ $key }})">
                                        <x-filament::loading-indicator class="h-5 w-5" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <x-filament::button wire:click="send_announcement({{ $record->id }})" class="mt-2 mb-4" color="success" icon="heroicon-o-paper-airplane">
                            <span wire:loading wire:target="send_announcement">Sending</span>
                            <span wire:loading.remove wire:target="send_announcement">Send</span>
                            Announcement
                        </x-filament::button>

                        <div wire:loading.flex wire:target="remove_recipient" class="gap-2">
                            <x-filament::loading-indicator class="h-5 w-5" />Deleting...
                        </div>
                    </div>
                @endif

                @error ('groups_dropdown_selection')
                    <span class="text-red-500 dark:text-red-300">{{ $message }}</span>
                @enderror

                <div class="flex items-center gap-2 recipient-datalist flex-col sm:flex-row">
                    <input wire:model="groups_dropdown_selection" list="groups-dropdown" class="bg-transparent placeholder:dark:text-gray-500 w-50-vw" id="groups-dropdown-choice" name="groups-dropdown-choice" placeholder="Select groups" />
                    <datalist id="groups-dropdown">
                        @foreach ($groups_dropdown as $group_name)
                            <option value="{{ $group_name }}"></option>
                        @endforeach
                    </datalist>

                    <x-filament::button wire:click.prevent="add_recipient" size="lg" class="py-2 px-3" color="info" icon="heroicon-o-plus">
                        <span wire:loading wire:target="add_recipient">Adding</span>
                        <span wire:loading.remove wire:target="add_recipient">Add</span>
                        Group Recipient
                    </x-filament::button>
                </div>
            @elseif ($active_choice == 'trainees')
                @if ($trainees_recipients)
                    <div class="border mb-4 p-4">
                        <h2>Announcement will be sent to {{ count($trainees_recipients) }} {{ count($trainees_recipients) > 1 ? 'trainees' : 'trainee' }}:</h2>
                        <ul class="flex flex-wrap gap-2">
                            @foreach ($trainees_recipients as $key => $trainee)
                                <li class="flex gap-2 border-2 border-solid border-green-500 rounded-xl py-1 px-2 w-fit">
                                    <span>{{ $trainee }}</span>
                                    <button wire:click.prevent="remove_recipient({{ $key }})" wire:loading.remove wire:target="remove_recipient({{ $key }})" title="Remove recipient">&times;</button>
                                    <div wire:loading.inline-flex wire:target="remove_recipient({{ $key }})">
                                        <x-filament::loading-indicator class="h-5 w-5" />
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <x-filament::button wire:click="send_announcement({{ $record->id }})" class="mt-2 mb-4" color="success" icon="heroicon-o-paper-airplane">
                            <span wire:loading wire:target="send_announcement">Sending</span>
                            <span wire:loading.remove wire:target="send_announcement">Send</span>
                            Announcement
                        </x-filament::button>

                        <div wire:loading.flex wire:target="remove_recipient" class="gap-2">
                            <x-filament::loading-indicator class="h-5 w-5" />Deleting...
                        </div>
                    </div>
                @endif

                @error ('trainees_dropdown_selection')
                    <span class="text-red-500 dark:text-red-300">{{ $message }}</span>
                @enderror

                <div class="flex items-center gap-2 recipient-datalist flex-col sm:flex-row">
                    <input wire:model="trainees_dropdown_selection" list="trainees-dropdown" class="bg-transparent placeholder:dark:text-gray-500 w-50-vw" id="trainees-dropdown-choice" name="trainees-dropdown-choice" placeholder="Select trainees" />
                    <datalist id="trainees-dropdown">
                        @foreach ($trainees_dropdown as $trainee_name)
                            <option value="{{ $trainee_name }}"></option>
                        @endforeach
                    </datalist>

                    <x-filament::button wire:click.prevent="add_recipient" size="lg" class="py-2 px-3" color="info" icon="heroicon-o-plus">
                        <span wire:loading wire:target="add_recipient">Adding</span>
                        <span wire:loading.remove wire:target="add_recipient">Add</span>
                        Recipient
                    </x-filament::button>
                </div>
            @endif
        </div>

        <div class="my-4">
            <x-label class="flex items-center gap-2" for="is_priority">
                <x-filament::input.checkbox wire:model="is_priority" />
                <span class="text-base">Mark as priority announcement?</span>
            </x-label>

            <div class="text-sm text-gray-500">Toggle this on if this announcement requires immediate attention by trainees. This will take precedence over an unprioritized latest announcement.</div>
        </div>
    </div>
</div>

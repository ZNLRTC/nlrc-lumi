<x-form-section submit="update_notification_settings">
    <x-slot name="title">
        {{ __('Notification Settings') }}
    </x-slot>

    <x-slot name="description">
        {{ __('You may opt-out of certain notifications here.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6">
            @if ($setting_on_call_meetings == 1)
                <x-checkbox wire:model="setting_on_call_meetings" id="setting_on_call_meetings" name="setting_on_call_meetings" checked />
            @else
                <x-checkbox wire:model="setting_on_call_meetings" id="setting_on_call_meetings" name="setting_on_call_meetings" />
            @endif

            <span class="dark:text-slate-300">Receive notifications for on-call meetings (on-going and upcoming)?</span>

            <x-input-error class="mt-2" for="setting_on_call_meetings" />
        </div>

        <div class="col-span-6">
            <x-button wire.loading.attr="disabled">
                <span wire:loading.flex wire:target="update_notification_settings">
                    <x-loading-indicator
                        :loader_color_bg="'fill-white'"
                        :loader_color_spin="'fill-white'"
                        :showText="false"
                        :size="4"
                    />

                    <span class="ml-2">Saving</span>
                </span>
                <span wire:loading.remove wire:target="update_notification_settings">Save</span>
            </x-button>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message :type="'success'" on="notification-settings-updated">Notification settings updated</x-action-message>
    </x-slot>
</x-form-section>

<x-form-section submit="update_timezone_settings">
    <x-slot name="title">
        {{ __('Timezone Settings') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Set your timezone. This will affect the times set on areas of the website, such as on-call meetings.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6">
            <x-label is_required="true" value="Timezone" for="timezone" />

            <x-select :inline_block="false" wire:model.live="timezone" class="w-full" id="timezone">
                <option value="">Select a timezone to use on this website</option>
                @foreach ($timezones as $timezone)
                    <option value="{{ $timezone }}">{{ $timezone }}</option>
                @endforeach
            </x-select>

            <small class="text-slate-700 dark:text-slate-300">{{ $current_time }}</small>

            <x-input-error class="mt-2" for="timezone" />
        </div>

        <div class="col-span-6">
            <x-button wire.loading.attr="disabled">
                <span wire:loading.flex wire:target="update_timezone_settings">
                    <x-loading-indicator
                        :loader_color_bg="'fill-white'"
                        :loader_color_spin="'fill-white'"
                        :showText="false"
                        :size="4"
                    />

                    <span class="ml-2">Saving</span>
                </span>
                <span wire:loading.remove wire:target="update_timezone_settings">Save</span>
            </x-button>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message :type="'success'" on="timezone-settings-updated">Timezone settings updated</x-action-message>
    </x-slot>
</x-form-section>

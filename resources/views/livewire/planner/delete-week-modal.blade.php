<x-filament::modal id="confirm-delete-week-info" width="sm">
    <x-slot name="heading">
        Delete this week's info?
    </x-slot>

    <x-slot name="description">
        Are you sure you want to delete the week's info for this group?
    </x-slot>

    <div class="flex gap-2 w-full">
        <x-filament::button wire:click="deleteWeekInfo" color="danger">
            Delete
        </x-filament::button>
        <x-filament::button wire:click="$dispatch('close-modal', {id: 'confirm-delete-week-info'})" color="gray">
            Cancel
        </x-filament::button>
    </div>

</x-filament::modal>
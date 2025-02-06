<x-filament::modal id="week-content-form" width="lg">
    <form wire:submit='create'>
        {{ $this->form }}
    
        <x-filament::button type="submit" class='mt-4'>
            Save
        </x-filament::button>
    </form>

</x-filament::modal>
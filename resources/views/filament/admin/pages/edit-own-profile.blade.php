<x-filament-panels::page>
    <x-filament-panels::form wire:submit="updateProfile"> 
        {{ $this->editProfileForm }}

        {{-- If there's a more elegant way to align these, I didn't find it --Mikko --}}
        <div class="justify-self-end">
            <x-filament-panels::form.actions :actions="$this->getUpdateProfileFormActions()" />
        </div>
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit="updatePassword">
        {{ $this->editPasswordForm }}

        <div class="justify-self-end">
            <x-filament-panels::form.actions :actions="$this->getUpdatePasswordFormActions()" />
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
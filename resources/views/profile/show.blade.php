<x-app-layout>
    @push('custom-styles')
        <link href="{{ asset('css/cropperjs/cropper.min.css') }}" rel="stylesheet" />
    @endpush

    <x-slot name="header">
        <h2 class="text-xl leading-tight text-slate-800 dark:text-slate-200">
            Edit your profile
        </h2>
    </x-slot>

    <div>
        <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <livewire:TimezoneSettings />

            <x-section-border />

            @if (\Auth::user()->hasRole('Trainee') && Laravel\Fortify\Features::canUpdateProfileInformation())
                <livewire:UpdateProfileInformationForm />

                <x-section-border />
            @endif

            @if (\Auth::user()->hasRole('Trainee'))
                <livewire:NotificationSettings />

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>

    @push('custom-scripts')
        <script src="{{ asset('js/cropperjs/cropper.min.js') }}"></script>
    @endpush
</x-app-layout>

<x-app-layout>
    @if (Auth::user()->hasRole('Trainee'))
        @push('custom-styles')
            <link href="{{ asset('css/custom/document-upload.css') }}" rel="stylesheet" />
        @endpush
    @endif

    <x-slot name="header">
        <h2 class="text-xl leading-tight text-slate-800 dark:text-slate-200">Dashboard</h2>
    </x-slot>

    {{-- Wrapper for the dashboard page sections --}}
    <div class="mt-4">
        <x-page-section class="hidden sm:block">
            <x-welcome />
        </x-page-section>

        <div class="grid grid-cols-1 gap-4 pb-4 mx-auto max-w-7xl sm:px-6 md:grid-cols-2 lg:px-8">
            <x-dashboard-section>
                <livewire:announcements.LatestAnnouncement />
            </x-dashboard-section>

            @if (Auth::user()->hasRole('Trainee'))
                <x-dashboard-section>
                    <livewire:dashboard.planner-trainee-summary />
                </x-dashboard-section>

                <x-dashboard-section>
                    <livewire:dashboard.document-completion-progress />
                </x-dashboard-section>

                <x-dashboard-section>
                    <livewire:dashboard.flag-trainee-history />
                </x-dashboard-section>
            @endif

            {{-- Only show it to active trainees whose active group is not Kyl mä hoidan and to instructors --}}
            @if (
                (Auth::user()->hasRole('Trainee') && Auth::user()->trainee->active == 1 && optional(Auth::user()->trainee->activeGroup)->group->name != 'Kyl mä hoidan') ||
                Auth::user()->hasRole('Instructor')
            )
                <x-dashboard-section>
                    <livewire:dashboard.on-call-meetings />
                </x-dashboard-section>
            @endif
        </div>
    </div>

    @if (Auth::user()->hasRole('Trainee'))
        @push('custom-scripts')
            <script src="{{ asset('js/custom/document-upload.js') }}"></script>
        @endpush
    @endif
</x-app-layout>

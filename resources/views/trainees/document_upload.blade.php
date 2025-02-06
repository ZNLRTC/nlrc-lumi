@push('custom-styles')
    <link href="{{ asset('css/custom/document-upload.css') }}" rel="stylesheet" />
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
            Upload documents
        </h2>
    </x-slot>

    <livewire:trainee.document-upload />

    @push('custom-scripts')
        <script src="{{ asset('js/custom/document-upload.js') }}"></script>
    @endpush
</x-app-layout>

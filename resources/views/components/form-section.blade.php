@props(['columns_on_breakpoint_md' => 3, 'submit'])

{{-- 
    Example usage:
    <x-form-section submit="function_name">
        <x-slot name="title">Title here</x-slot>
        <x-slot name="description">Description here</x-slot>
        <x-slot name="form">Form here</x-slot>
        <x-slot name="actions">Actions here</x-slot>
    </x-form-section>
--}}

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-' .$columns_on_breakpoint_md. ' md:gap-6']) }}>
    @if (isset($title) || isset($description))
        <x-section-title>
            @if (isset($title))
                <x-slot name="title">{{ $title }}</x-slot>
            @endif

            @if (isset($description))
                <x-slot name="description">{{ $description }}</x-slot>   
            @endif
        </x-section-title>
    @endif

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-4 py-5 bg-white dark:bg-nlrc-blue-800 sm:p-6 shadow {{ isset($actions) ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md' }}">
                <div class="grid grid-cols-6 gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-start px-4 py-3 bg-nlrc-blue-50 dark:bg-nlrc-blue-800 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>

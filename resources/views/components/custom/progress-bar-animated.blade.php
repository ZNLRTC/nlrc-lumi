@props([
    'progress_bar_colors' => ['bg-green-300', 'dark:bg-green-400'],
    'progress_bar_value' => 0,
    'text_alignment' => 'left',
    'transition_speed' => 1.75,
    // Dynamic. Must put correct livewire property from component (omit the $)
    'use_livewire_property' => true,
    'livewire_property' => '$wire.' .$attributes['livewire_property_to_use'],
])

@php
    if (!$use_livewire_property) {
        $livewire_property = $progress_bar_value;
    }
@endphp

<div
    x-data="{ width: 0 }"
    x-init="
        if ({{ $livewire_property }} > 0) {
            width = {{ $livewire_property }};
        }
    "
    {{ $attributes->merge(['class' => 'items-center grid grid-cols-1 progress-bar-animated-container']) }}
>
    <div
        x-bind:style="width > 0 ? `width: ${width}%; transition: width {{ $transition_speed }}s;` : ''"
        class="rounded text-center w-0 h-4 {{ implode(' ', $progress_bar_colors) }}"
    ></div>

    <p class="{{ $text_alignment == 'right' ? 'text-end' : '' }}"><span x-text="width">0</span>%</p>
</div>

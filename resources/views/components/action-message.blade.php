@props([
    'on', // Name of dispatched event
    'font_size' => 'sm',
    'type' => null
])

@php
    switch($type) {
        case 'success':
            $text_colors = 'text-green-500 dark:text-green-300';

            break;
        case 'danger':
            $text_colors = 'text-red-500 dark:text-red-300';

            break;
        default:
            $text_colors = 'text-slate-600 dark:text-slate-400';

            break;
    }
@endphp

<div x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    style="display: none;"
    {{ $attributes->merge(['class' => 'text-' .$font_size. ' ' .$text_colors]) }}>
    {{ $slot->isEmpty() ? 'Saved.' : $slot }}
</div>

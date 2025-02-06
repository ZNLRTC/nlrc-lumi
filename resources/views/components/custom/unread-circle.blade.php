@props([
    'is_read',
    'tooltip' => 'Unread notification'
])

@if (!$is_read)
    <sup>
        <div class="inline-block h-4 w-4 scale-100 unread-circle-icon" title="{{ $tooltip }}"></div>
    </sup>
@endif

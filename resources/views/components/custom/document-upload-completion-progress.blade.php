@props(['completion_progress_text' => '0 of 15 documents completed', 'completion_progress' => '0', 'progress_percent_classes'])

<div wire:ignore.self {{ $attributes->merge(['class' => 'grid place-items-center relative completion-progress']) }} x-data="themeSwitcher()" :class="{ 'dark': switchOn }">
    <div wire:ignore.self class="{{ $progress_percent_classes }} dark:text-white progress-percent" id="progress-percent" title="{{ $completion_progress_text }}" data-completion-progress="{{ $completion_progress }}" x-data="themeSwitcher()" :class="{ 'dark': switchOn }">{{ $completion_progress. '%' }}</div>
</div>

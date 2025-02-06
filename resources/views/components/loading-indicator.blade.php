@props([
    'loader_color_bg' => 'fill-nlrc-blue-500',
    'loader_color_spin' => 'fill-nlrc-blue-500',
    'target' => null,
    'text' => 'Loading...',
    'text_color' => 'text-slate-400 dark:text-slate-500',
    'showText' => true,
    'size' => '10',
])

<div {{ $attributes->merge(['class' => 'flex justify-center items-center']) }} {{ $target ? "wire:loading wire:target=$target" : "wire:loading" }}>
    <svg class="inline-block align-middle animate-spin h-{{ $size }} w-{{ $size }}" width="512px" height="512px" viewBox="0 0 24 24" fill="#0f72ba" x="0" y="0" role="img" xmlns="http://www.w3.org/2000/svg">
        <g fill="#0f72ba">
            {{-- Ring background --}}
            <path class="{{ $loader_color_bg }}" opacity=".35" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" />
            {{-- Ring that spins --}}
            <path class="{{ $loader_color_spin }}" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z" />
        </g>
    </svg>

    @if ($showText)
        <span class="ms-2 {{ $text_color }}">{{ $text }}</span>
    @endif
</div>

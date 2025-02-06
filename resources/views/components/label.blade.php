@props(['value', 'is_required' => false])

<label {{ $attributes->merge(['class' => 'block font-sans text-sm text-slate-700 dark:text-slate-300']) }}>
    {{ $value ?? $slot }}

    @if ($is_required)
        <x-required />
    @endif
</label>

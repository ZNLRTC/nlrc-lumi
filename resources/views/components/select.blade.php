@props(['disabled' => false, 'inline_block' => true])

<div class="relative {{ $inline_block ? ' inline-block' : ''}}">
    <select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full border-slate-300 dark:border-nlrc-blue-700 dark:bg-nlrc-blue-900 dark:text-slate-300 focus:border-nlrc-blue-500 dark:focus:border-nlrc-blue-500 focus:ring-nlrc-blue-500 dark:focus:ring-nlrc-blue-500 rounded-md shadow-sm appearance-none disabled:bg-nlrc-blue-100 dark:disabled:bg-nlrc-blue-700 disabled:text-slate-400 dark:disabled:text-slate-500']) !!}>
        {{ $slot }}
    </select>
    <div class="pointer-events-none absolute right-0 top-4 text-slate-700 px-2">
        <svg class="h-4 w-4 {{ $disabled ? 'fill-slate-400 dark:fill-slate-500' : 'fill-slate-600 dark:fill-slate-200' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M10 12l-6-6h12l-6 6z"/>
        </svg>
    </div>
</div>
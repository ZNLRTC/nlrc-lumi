@props(['disabled' => false])

@php
$classes = ($disabled)
            ? 'inline-flex items-center px-4 py-2 bg-nlrc-blue-300 dark:bg-nlrc-blue-700 dark:text-slate-900 border border-transparent rounded-md font-semibold text-xs text-slate-100 uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150'
            : 'inline-flex items-center px-4 py-2 bg-nlrc-blue-500 dark:bg-nlrc-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-nlrc-slate-300 uppercase tracking-widest hover:bg-nlrc-blue-600 dark:hover:bg-nlrc-blue-500 focus:bg-nlrc-blue-600 dark:focus:bg-nlrc-blue-500 active:bg-nlrc-blue-500 dark:active:bg-nlrc-blue-700 focus:outline-none focus:ring-2 focus:ring-nlrc-blue-600 focus:ring-offset-2 dark:focus:ring-offset-nlrc-blue-700 transition ease-in-out duration-150';
@endphp

<button {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
    {{ $slot }}
</button>
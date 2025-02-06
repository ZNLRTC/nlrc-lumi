@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 dark:border-b-4 border-nlrc-blue-600 dark:border-nlrc-blue-700 text-sm font-medium leading-5 text-white dark:text-slate-100 focus:outline-none focus:border-nlrc-blue-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 dark:border-b-4 border-transparent text-sm font-medium leading-5 text-white dark:text-slate-400 hover:text-slate-300 dark:hover:text-slate-300 hover:border-nlrc-blue-600 dark:hover:border-nlrc-blue-700 focus:outline-none focus:text-slate-300 dark:focus:text-slate-300 focus:border-nlrc-blue-700 dark:focus:border-nlrc-blue-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

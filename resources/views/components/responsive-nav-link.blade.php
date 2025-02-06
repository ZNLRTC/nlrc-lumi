@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex justify-start items-center gap-2 w-full ps-3 pe-4 py-2 border-l-4 border-nlrc-blue-700 dark:border-nlrc-blue-600 text-start text-base font-medium text-white dark:text-slate-300 bg-nlrc-blue-600 dark:bg-nlrc-blue-700 focus:outline-none focus:text-white dark:focus:text-nlrc-blue-600 focus:bg-nlrc-blue-600 dark:focus:bg-nlrc-blue-900 focus:border-nlrc-blue-600 dark:focus:border-nlrc-blue-700 transition duration-150 ease-in-out'
            : 'flex justify-start items-center gap-2 w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-200 dark:text-slate-400 hover:text-white dark:hover:text-slate-200 hover:bg-nlrc-blue-600 dark:hover:bg-nlrc-blue-800 hover:border-nlrc-blue-600 dark:hover:border-nlrc-blue-800 focus:outline-none focus:text-white dark:focus:text-slate-200 focus:bg-nlrc-blue-600 dark:focus:bg-nlrc-blue-600 focus:border-nlrc-blue-700 dark:focus:border-nlrc-blue-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

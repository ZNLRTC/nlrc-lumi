@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block min-h-[100px] w-full border-nlrc-blue-300 dark:border-nlrc-blue-700 dark:bg-nlrc-blue-900 dark:text-slate-300 focus:border-nlrc-blue-500 dark:focus:border-nlrc-blue-600 focus:ring-nlrc-blue-500 dark:focus:ring-nlrc-blue-500 rounded-md shadow-sm disabled:bg-nlrc-blue-100 disabled:text-slate-500 dark:disabled:bg-nlrc-blue-700 dark:disabled:text-slate-400 placeholder:italic placeholder:text-slate-400 dark:placeholder:text-slate-500']) !!}>
    {{ $slot }}
</textarea>

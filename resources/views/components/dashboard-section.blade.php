<div {{ $attributes->merge(['class' => 'bg-white dark:bg-nlrc-blue-800 overflow-hidden shadow sm:shadow-md sm:rounded-lg h-full w-full']) }}>
    <div class="p-4 lg:p-8 bg-white dark:bg-nlrc-blue-800 dark:bg-gradient-to-bl dark:from-slate-700/50 dark:via-transparent text-slate-900 dark:text-slate-200">

        {{ $slot }}

    </div>
</div>
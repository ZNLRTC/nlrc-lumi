<div {{ $attributes->merge(['class' => 'pb-4 first:mt-8']) }}>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-nlrc-blue-800 overflow-hidden shadow sm:shadow-md sm:rounded-lg">   
            <div class="p-4 lg:p-8 bg-white dark:bg-nlrc-blue-800 dark:bg-gradient-to-bl dark:from-slate-700/50 dark:via-transparent text-slate-900 dark:text-slate-200">

                {{ $slot }}

            </div>
        </div>
    </div>
</div>
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-nlrc-blue-800 border border-nlrc-blue-300 dark:border-nlrc-blue-500 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-nlrc-blue-50 dark:hover:bg-nlrc-blue-700 focus:outline-none focus:ring-2 focus:ring-nlrc-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

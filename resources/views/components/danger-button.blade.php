<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 dark:hover:bg-red-950 active:bg-red-700 dark:active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

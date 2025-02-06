<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl text-slate-800 dark:text-slate-200 leading-tight">
            <a class='text-slate-500 hover:text-slate-400 dark:text-slate-400 dark:hover:text-slate-300' href="{{ route('kb.index') }}">Help and instructions</a> <span class='ps-0 sm:ps-2 text-slate-400 dark:text-slate-600'>&raquo;</span> <span class="ps-0 sm:ps-2">{{ $category->name }}</span>
        </h2>
    </x-slot>

    <livewire:k-b.show :category="$category" :article="$article"/>

</x-app-layout>
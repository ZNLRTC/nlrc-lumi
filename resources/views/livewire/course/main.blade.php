<div>
    <div class='max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 dark:text-white'>
        <p>{{ $course->description }}</p>
        <p class="mt-2">This course {{ $course->units_count > 0 ? "has $course->units_count units. Click on them to start studying" : 'is empty' }}.</p>
    </div>

    <div class="max-w-7xl mx-auto grid gap-4 content-stretch grid-cols-1 md:grid-cols-2 lg:grid-cols-3 pb-4 sm:px-6 lg:px-8">
        @foreach ($course->units as $unit)

            <a href="{{ route('units.index', ['course' => $course->slug, 'unit' => $unit->slug]) }}" class="group">
                <x-dashboard-section class="hover:text-nlrc-blue-600 dark:hover:text-sky-600 hover:ring-1 hover:ring-nlrc-blue-500">
                    <p class="mb-2 font-semibold text-nlrc-blue-500 dark:text-sky-500 group-hover:text-nlrc-blue-600 dark:group-hover:text-sky-600 group-focus:text-nlrc-blue-600 dark:group-focus:text-sky-600">{{ $unit->name }}</p>
                    <p class="group-hover:text-nlrc-blue-600 dark:group-hover:text-sky-600 group-focus:text-nlrc-blue-600 dark:group-focus:text-sky-600">{{ $unit->description }}</p>
                    <p class="mt-2 italic text-base md:text-sm text-right text-nlrc-blue-500 dark:text-sky-500 group-hover:text-nlrc-blue-600 dark:group-hover:text-sky-600 group-focus:text-nlrc-blue-600 dark:group-focus:text-sky-600">&rarr; click to open</p>
                </x-dashboard-section>
            </a>

        @endforeach
    </div>
        
</div>

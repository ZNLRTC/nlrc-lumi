<div>
    <div class='max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 dark:text-slate-200'>

        <p>These are the meetings you must complete during your Finnish studies. You may have several meeting attempts per unit if you did not demonstrate that you master the unit on the first try. You must eventually have a successfully completed attempt for each unit.</p>

    </div>

    {{-- Handles the situation where trainee has no group --}}
    @forelse (optional($trainee->activeGroup)->group->courses ?? [] as $course)

        <x-page-section>

            <h2 class="text-xl">{{ $course->name }} course</h2>

            @forelse($course->units as $unit)

                <h3 class='mt-4 border-t pt-2 border-nlrc-blue-200 dark:border-nlrc-blue-600'>{{ $unit->name }}</h3>

                @foreach($unit->meetings as $meeting)

                    @include('meetings.partials.meetings-of-unit', ['meeting' => $meeting])

                {{-- This doesn't need a "no units" placeholder since units without meetings are already excluded in the component --}}

                @endforeach
                    
            @empty

                <li class='block border p-2 text-sm rounded bg-nlrc-blue-50 border-nlrc-blue-100'>
                    <p class='text-slate-600'>This course has no meetings.</p>
                </li>

            @endforelse

        </x-page-section>

    @empty
        <div class='max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 dark:text-slate-200'>
            <p class='text-slate-600'>You are not currently assigned to any group. Your past meetings will (re-)appear here once you have been added to a group.</p>
        </div>
    @endforelse

</div>
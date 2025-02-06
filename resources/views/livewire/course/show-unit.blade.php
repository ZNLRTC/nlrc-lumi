<div class="pb-10">
    <div class='max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 dark:text-white'>
        <p>{{ $unit->description }}</p>
    </div>

    @if (auth()->user()->hasRole('Trainee') && $unit->course->slug !== 'kmh')
        <livewire:meetings.unit-meetings :unit="$unit" />

        @if ($unit->assignments_count)
            <x-page-section>
                <h2 class='text-lg'>Assignments in this unit</h2>
                @if ($unit->assignments_count > 1)
                    <p>This unit has {{ $unit->topics_count }} assignments that you have to submit prior to your meeting. Click on the assignment name to access them.</p>
                    @foreach ($unit->assignments as $assignment)
                        <a href="{{ route('assignments.create', ['course' => $unit->course->slug, 'unit' => $unit->slug, 'assignment' => $assignment->slug]) }}">
                            <div class='block sm:inline-block w-full sm:w-fit py-2 px-4 me-2 mt-2 bg-nlrc-blue-500 hover:bg-nlrc-blue-600 focus:bg-nlrc-blue-600 active:bg-nlrc-blue-600 dark:bg-nlrc-blue-600 dark:hover:bg-nlrc-blue-500 dark:focus:bg-nlrc-blue-500 dark:active:bg-nlrc-blue-500 text-white dark:text-slate-200 rounded'>
                                {{ $assignment->name }}
                            </div>
                        </a>
                    @endforeach
                @else
                    <p>This unit has one assignment that you have to submit prior to your meeting. Click to access it:</p>
                    <a href="{{ route('assignments.create', ['course' => $unit->course->slug, 'unit' => $unit->slug, 'assignment' => $unit->assignments->first()->slug]) }}">
                        <div class='block sm:inline-block w-full sm:w-fit mt-2 lg:mt-4 py-2 px-4 bg-nlrc-blue-500 hover:bg-nlrc-blue-600 focus:bg-nlrc-blue-600 active:bg-nlrc-blue-600 dark:bg-nlrc-blue-600 dark:hover:bg-nlrc-blue-500 dark:focus:bg-nlrc-blue-500 dark:active:bg-nlrc-blue-500 text-white dark:text-slate-200 rounded'>{{ $unit->assignments->first()->name }}</div>
                    </a>
                @endif
            </x-page-section>
        @endif
    @endif

    <div class='max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 dark:text-white'>
        <p>
            {{ ($unit->topics_count ?? 0) > 0 ? 'You can click on the headings to expand and collapse the topics below.' : 'There are no topics at the moment.' }}
        </p>
    </div>
        
    @foreach ($unit->topics as $topic)
        <x-topic-box id="{{ $topic->slug }}" topic-id="{{ Crypt::encrypt($topic->id) }}">
            <x-slot name="title">
                {{ $topic->title }}
            </x-slot>

            <x-slot name="description">
                {{ $topic->description }}
            </x-slot>

            <x-slot name="content">
                {!! $topic->content !!}
            </x-slot>
        </x-topic-box>
        
    @endforeach
</div>

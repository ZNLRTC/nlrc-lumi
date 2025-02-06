@php
    use \App\Enums\Planner\ContentType;
@endphp

@if ($content_type === ContentType::BREAK_WEEK)
    <p class='p-2'>Brush-up week: Review past content. No need to study new content. No meetings.</p>
@elseif ($content_type === ContentType::NONE)
    <p class='p-2'>No studies or meetings.</p>
@else
    <div class='flex flex-col sm:flex-row gap-2 divide-x-0 sm:divide-x divide-y sm:divide-y-0 divide-slate-100 dark:divide-slate-900 mb-2 pt-2'>
        @if ($content_type === ContentType::DEFAULT || $content_type === ContentType::UNIT_ONLY)
            <div class='px-2 flex-1'>
                <p class='tiny-heading text-slate-500 dark:text-slate-400'>Content to study</p>
                <ul class='ms-6 list-outside'>
                    @foreach($units as $unit)
                        <li class='list-square'>
                            <a href="{{ route('units.index', ['course' => $unit->course->slug, 'unit' => $unit->slug]) }}" class="text-nlrc-blue-500 dark:text-sky-400 hover:text-nlrc-blue-600 focus:text-nlrc-blue-600 dark:hover:text-sky-500 dark:focus:text-sky-500">
                                {{ strtolower($unit->name) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($content_type === ContentType::DEFAULT || $content_type === ContentType::MEETING_ONLY)
            <div class='px-2 pt-2 sm:pt-0 first:pt-0 flex-1'>
                <p class='tiny-heading text-slate-500 dark:text-slate-400'>Meetings</p>
                <ul class='ms-6 list-outside'>
                    @foreach($meetings as $meeting)
                        <li class='list-square'>{{ strtolower($meeting->description) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
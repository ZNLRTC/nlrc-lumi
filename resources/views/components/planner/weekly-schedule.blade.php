@php
    use \App\Enums\Planner\ContentType;
@endphp

<div>
    @if($weeklySchedule)
        <div class='p-2 bg-nlrc-blue-100 dark:bg-nlrc-blue-900 flex justify-between gap-2'>
            <h3>{{ $title }}</h3>
            <p class="text-slate-600 dark:text-slate-500">{{ $dateRange }}</p>
        </div>
        
        @if ($weeklySchedule->content_type === ContentType::CUSTOM_CONTENT && $weeklySchedule->custom_content)
            <p class="p-2 nlrc markdown">{!! $weeklySchedule->custom_content !!}</p>
        @else
            @include('components.planner.schedule-content', [
                'content_type' => $weeklySchedule->content_type,
                'units' => $units,
                'meetings' => $meetings
            ])
            @if($weeklySchedule->custom_content && $weeklySchedule->show_custom_content)
                <p class="p-2 border-t border-nlrc-blue-100 dark:border-nlrc-blue-900 nlrc markdown">{!! $weeklySchedule->custom_content !!}</p>
            @endif
        @endif

    @else
        <p class="p-2 bg-nlrc-blue-100 dark:bg-nlrc-blue-900">No schedule released for {{ strtolower($title) }} yet.</p>
    @endif
</div>
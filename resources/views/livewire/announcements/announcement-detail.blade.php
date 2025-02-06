<div class="p-4">
    <h3 class="text-lg font-semibold mb-2 text-slate-800 dark:text-slate-200">{{ $current_announcement['title'] }}</h3>
    <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm">Posted by {{ $current_announcement->user->name }} on <span class="bold">{{ \Carbon\Carbon::parse($current_announcement['created_at'])->format('D, M j, Y \a\t g:i A') }}</span></p>

    @if ($current_announcement['created_at'] != $current_announcement['updated_at'])
        <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm">Updated on <span class="bold">{{ \Carbon\Carbon::parse($current_announcement['updated_at'])->format('D, M j, Y \a\t g:i A') }}</span></p>
    @endif

    @php
        $thumbnail_path = $current_announcement['thumbnail_image_path'] ? Storage::disk('announcements')->url($current_announcement['thumbnail_image_path']) : asset('img/default-thumbnail-announcement.jpg');
    @endphp

    <img src="{{ $thumbnail_path }}" width="100%" alt="Latest announcement image" title="{{ $current_announcement['title'] }} image" loading="lazy" />

    <div class="mt-4 dark:text-slate-400 nlrc markdown">{!! Markdown::parse($current_announcement['description']) !!}</div>

    <div class="border-t border-nlrc-blue-200 dark:border-nlrc-blue-900 mt-4 pt-2 flex flex-row justify-between items-center">
        <p><a class='text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-600' href="{{ route('announcements.index') }}">&larr; Back</a></p>
    </div>
</div>

<div class="w-full">
    @if ($announcement)
        <h2 class="flex items-center gap-2 mb-2 text-xl dark:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                <path d="M13.92 3.845a19.362 19.362 0 0 1-6.3 1.98C6.765 5.942 5.89 6 5 6a4 4 0 0 0-.504 7.969 15.97 15.97 0 0 0 1.271 3.34c.397.771 1.342 1 2.05.59l.867-.5c.726-.419.94-1.32.588-2.02-.166-.331-.315-.666-.448-1.004 1.8.357 3.511.963 5.096 1.78A17.964 17.964 0 0 0 15 10c0-2.162-.381-4.235-1.08-6.155ZM15.243 3.097A19.456 19.456 0 0 1 16.5 10c0 2.43-.445 4.758-1.257 6.904l-.03.077a.75.75 0 0 0 1.401.537 20.903 20.903 0 0 0 1.312-5.745 2 2 0 0 0 0-3.546 20.902 20.902 0 0 0-1.312-5.745.75.75 0 0 0-1.4.537l.029.078Z" />
            </svg>
            <a href="{{ route('announcements.index') }}" class="text-nlrc-blue-500 dark:text-sky-500 hover:text-nlrc-blue-600 dark:hover:text-sky-400">Latest Announcement</a>
        </h2>

        <div class="rounded dark:bg-nlrc-blue-700 border dark:border-0 border-nlrc-blue-200">
            {{-- Setting max-height to this because someone will upload some dumb 100x2000px image at some point anyway --}}
            <div style="background-image: url('{{ $announcement->thumbnail_image_path ? Storage::disk('announcements')->url($announcement->thumbnail_image_path) : asset('img/default-thumbnail-announcement.jpg') }}')" class="h-32 bg-cover bg-center rounded-t w-full border-b dark:border-b-0 border-nlrc-blue-200" ></div>

            {{--
            @php
                $thumbnail_path = $announcement->thumbnail_image_path ? asset('storage/' .$announcement->thumbnail_image_path) : asset('storage/defaults/default-thumbnail-announcement.jpg');
            @endphp

            <img src="{{ $thumbnail_path }}" width="100%" alt="Latest announcement image" title="{{ $announcement->title }} image" loading="lazy" />
            --}}

            <div class="p-4">
                <h2 class="text-xl dark:text-white font-bold">{{ $announcement->title }}</h2>

                @if (str_word_count($announcement->description) > 200)
                    <div class="mt-2 indent-4 text-justify line-clamp-3 dark:text-slate-400">{!! Markdown::parse($announcement->description) !!}</div>
                    <a wire:navigate href="{{ route('announcements.detail', ['id' => $announcement->id]) }}" class="d-inline-block mb-4 text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-400" title="Click me to read more">Read more</a>
                @else
                    <div class="mt-2 indent-4 text-justify dark:text-slate-400">{!! Markdown::parse($announcement->description) !!}</div>
                @endif
            </div>
        </div>
    @else
        <p class="dark:text-white">No latest announcement found.</p>
    @endif
</div>

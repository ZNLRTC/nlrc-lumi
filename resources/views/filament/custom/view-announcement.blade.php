@php
    $url_array = explode('/', url()->current());
    $announcement_id = (int) $url_array[count($url_array) - 1];
    $record = \App\Models\Announcement::findOrFail($announcement_id);
@endphp

<div class="flex flex-col gap-y-4 py-8">
    <nav class="fi-breadcrumbs mb-2 hidden sm:block">
        <ol class="fi-breadcrumbs-list flex flex-wrap items-center gap-x-2">
            <li class="fi-breadcrumbs-item flex gap-x-2">
                <a wire:navigate href="{{ \App\Filament\Admin\Resources\AnnouncementResource::getUrl() }}" class="fi-breadcrumbs-item-label text-sm font-medium text-gray-500 transition duration-75 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Announcements
                </a>
            </li>

            <li class="fi-breadcrumbs-item flex gap-x-2">
                <svg class="fi-breadcrumbs-item-separator flex h-5 w-5 text-gray-400 dark:text-gray-500 rtl:hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                </svg>

                <svg class="fi-breadcrumbs-item-separator flex h-5 w-5 text-gray-400 dark:text-gray-500 ltr:hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"></path>
                </svg>

                <span href="#" class="fi-breadcrumbs-item-label text-sm font-medium text-gray-500 transition duration-75 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Detail
                </span>
            </li>
        </ol>
    </nav>

    <div class="w-full pb-10">
        <h1 class="text-4xl font-bold">{{ $record->title }}</h1>
        <p class="text-start py-2">Posted by <span>{{ $record->user->name }}</span> on <strong>{{ \Carbon\Carbon::parse($record->created_at)->format('F j, Y g:i:s A') }}</strong></p>

        <div class="indent-2">
            {!! Markdown::parse($record->description) !!}
        </div>
    </div>
</div>

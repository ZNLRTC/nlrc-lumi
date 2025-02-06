@php
    $record = $getRecord();
@endphp

<div class="flex flex-col gap-2">
    <label class="text-sm">Profile photo</label>
    <img src="{{ $record->user->profilePhotoUrl() }}" class="h-20 w-20 rounded-full object-cover" alt="User profile photo" title="User profile photo" />

    <div class="w-fit py-2 px-3 rounded-md text-sm bg-orange-600 hover:bg-orange-400 dark:bg-orange-500 dark:hover:bg-orange-700">
        <a class="text-white" href="{{ $record->user->profilePhotoUrl() }}" title="View this upload" target="_blank">View original resolution</a>
    </div>
</div>

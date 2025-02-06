<div>
    <h2 class="flex gap-2">
        <x-heroicon-o-document-text class="h-6 w-auto" />
        <span class="dark:text-white text-lg font-medium">Document Completion</span>

        <sup class="text-nlrc-blue-500 dark:text-sky-500 hover:text-nlrc-blue-600 dark:hover:text-sky-400">
            <button title="Open popover" popovertarget="document-completion-popover">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </button>
        </sup>
    </h2>

    <x-custom.document-upload-completion-progress
        class="h-20 w-20"
        :completion_progress_text="$completion_progress_text"
        :completion_progress="$completion_progress"
        :progress_percent_classes="'text-xl'"
    />

    <div class="mx-auto w-3/4 md:w-1/2 p-4 border bg-nlrc-blue-100 border-nlrc-blue-400 dark:bg-nlrc-blue-800 dark:border-nlrc-blue-600" id="document-completion-popover" popover>
        <div class="flex justify-between">
            <h3 class="text-lg dark:text-white">Document Completion</h3>
            <button class="text-xl dark:text-white" title="Close popover" popovertarget="document-completion-popover" popovertargetaction="hide">&times;</button>
        </div>

        <div class="overflow-y-auto pr-6 max-h-75-vh">
            <div>
                <span class="dark:text-slate-400">LEGEND:</span>

                <ul class="my-2 dark:text-slate-400">
                    @foreach (\App\Enums\DocumentTraineesStatus::cases() as $status)
                        <li class="{{ \App\Enums\DocumentTraineesStatus::textColor($status) }}">{{ $status->value }}</li>
                    @endforeach
                    <li class="dark:text-slate-400">Not yet uploaded</li>
                </ul>

                <span class="text-xs dark:text-slate-400">{{ $completion_progress_text }} ({{ $completion_progress }}%)</span>
            </div>

            <ul class="my-5 dark:text-slate-400">
                @foreach ($trainee_documents as $document)
                    @php
                        $status = '';
                        switch ($document['status']) {
                            case \App\Enums\DocumentTraineesStatus::APPROVED->value:
                                $status = \App\Enums\DocumentTraineesStatus::APPROVED;

                                break;
                            case \App\Enums\DocumentTraineesStatus::RE_UPLOAD_NEEDED->value:
                                $status = \App\Enums\DocumentTraineesStatus::RE_UPLOAD_NEEDED;

                                break;
                            case \App\Enums\DocumentTraineesStatus::PENDING_CHECKING->value:
                                $status = \App\Enums\DocumentTraineesStatus::PENDING_CHECKING;

                                break;
                        }
                    @endphp

                    <li class="flex gap-4 text-start items-center">
                        <input type="checkbox" class="text-slate-400 bg-nlrc-blue-200 dark:text-slate-700 dark:bg-nlrc-blue-500" disabled {{ $status == \App\Enums\DocumentTraineesStatus::APPROVED ? 'checked' : '' }} />
                        <span class="{{ $status ? \App\Enums\DocumentTraineesStatus::textColor($status) : 'dark:text-slate-400' }}">{{ $document['name'] }}</span>
                    </li>
                @endforeach
            </ul>

            <i class="text-xs dark:text-slate-400">Click the X above or click anywhere to close this popover.</i>
        </div>
    </div>
</div>

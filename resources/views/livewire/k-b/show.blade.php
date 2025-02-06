<x-page-section>

    @if ($article->status === 'Draft')
        <div class="bg-red-100 border-l-4 dark:bg-red-900 border-red-700 dark:border-red-600 text-red-700 dark:text-red-200 p-4 mb-4" role="alert">
            <p class="font-bold">This article is a draft.</p>
            <p>It is not yet visible to its audience.</p>
        </div>
    @endif
    <h3 class="text-lg font-semibold mb-2 text-slate-800 dark:text-slate-200">{{ $article->title }}</h3>
    <div class="nlrc kb">{!! $articleContentHtml !!}</div>

    {{-- Voting --}}

    @if (auth()->user()->hasAnyRole(['Trainee', 'Instructor']) && !$hasVoted)
        <div class='w-full flex justify-center my-4'>
            <div class="p-4 border border-nlrc-blue-100 dark:border-nlrc-blue-900 bg-slate-50 dark:bg-nlrc-blue-900 rounded flex flex-col justify-center gap-2">
                <p class='text-sm text-center'>Was this article helpful?</p>
                <div class="flex gap-4 justify-center">
                    <x-button wire:click="markHelpful({{ $article->id }}, true)" class="px-2 py-1 bg-green-500 text-white rounded">
                        <x-heroicon-m-hand-thumb-up class="h-4 w-auto me-2" />
                        Yes
                    </x-button>
                    <x-danger-button wire:click="markHelpful({{ $article->id }}, false)" class="px-2 py-1 bg-red-500 text-white rounded">
                        <x-heroicon-m-hand-thumb-down class="h-4 w-auto me-2"/>
                        No
                    </x-danger-button>
                </div>
        </div>
        </div>
    @endif

    @if ($showFeedbackForm)
        <div class='w-full flex justify-center my-4'>
            <div class="p-4 border border-nlrc-blue-100 dark:border-nlrc-blue-900 bg-slate-50 dark:bg-nlrc-blue-900 rounded flex flex-col justify-center gap-2">
                <p class='text-sm text-center'>How could we improve the article?</p>
                <div class="mt-2">
                    <x-textarea wire:model="feedback"></x-textarea>
                    <x-input-error for="feedback" class="mt-2" />
                    <div class="flex justify-center mt-2">
                        <x-button wire:click="submitFeedback">Submit</x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('postVoteMessage'))
        <div class='w-full flex justify-center my-4'>
            <div class="p-4 text-sm border border-nlrc-blue-100 dark:border-nlrc-blue-900 bg-slate-50 dark:bg-nlrc-blue-900 rounded flex flex-col justify-center gap-2">
                {{ session()->get('postVoteMessage') }}
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="border-t border-nlrc-blue-200 dark:border-nlrc-blue-900 mt-4 pt-2 flex flex-row justify-between items-center">

        <p><a class='text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-600' href="{{ route('kb.index') }}">&larr; back</a></p>

        @if ($article->updated_at && $article->updated_at != $article->created_at)
            <p class="text-xs sm:text-sm text-right italic text-slate-400 dark:text-slate-500">Updated on {{ \Carbon\Carbon::parse($article->updated_at)->inUserTimezone()->format('D, M j, Y, H:i') }}</p>
        @else
            <p class="text-xs sm:text-sm text-right italic text-slate-500 dark:text-slate-500">Created on {{ \Carbon\Carbon::parse($article->created_at)->inUserTimezone()->format('D, M j, Y, H:i') }}</p>
        @endif

    </div>
</x-page-section>
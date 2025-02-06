<div>
    <x-page-section>

        <p class="mb-2">Search or pick a category to find support articles.</p>

        <div class="relative w-full">
            <x-input type="text" wire:model.live.debounce.500ms="search" placeholder="Enter a keyword to search..." class="w-full p-2 mb-4 border rounded"/>
            @if($search)
                <button wire:click="$set('search', '')" class="text-3xl absolute right-3 inset-y-0 -top-4 text-slate-400 hover:text-slate-700 dark:text-slate-600 dark:hover:text-slate-300">
                    &times;
                </button>
             @endif
        </div>

        @if($search)
            <div class="mb-4">
                <div wire:loading.block>
                    <x-loading-indicator :showText="false" />
                </div>
                <h2>Search results:</h2>
                @forelse($searchResults as $article)
                    <a wire:key="search-result-{{ $article->id }}" class="block p-2 my-1 rounded border border-nlrc-blue-200 dark:border-nlrc-blue-900 hover:text-nlrc-blue-500 dark:hover:text-slate-400 hover:ring-1 hover:ring-nlrc-blue-500" href="{{ route('kb.show', ['category' => $article->category->slug, 'article' => $article->slug]) }}">
                        <p class="font-bold">{{ $article->title }}</p>
                        <p>{{ $article->summary }}</p>
                        <p class="text-nlrc-blue-500 dark:text-sky-600 italic">&rarr; click to read</p>
                    </a>
                @empty
                    <p>No articles found.</p>
                @endforelse
            </div>
        @endif
    </x-page-section>

    <x-page-section>
        <p class="mb-2">Article categories:</p>
        @foreach($this->kbCategories as $category)

            <div wire:key="category-{{ $category->id }}" x-data="{ open:false }" class="p-2 my-1 rounded border border-slate-200 dark:border-nlrc-blue-900">
                <button @click="open = !open" class="w-full h-full text-start text-nlrc-blue-500 dark:text-slate-300 hover:text-nlrc-blue-600 dark:hover:text-slate-400 flex justify-between items-center">
                    {{ $category->name }}
                    <div :class="{'rotate-180': open, 'rotate-0': !open}" class="transition-transform duration-500">
                        <x-heroicon-o-chevron-down class='text-nlrc-blue-500 dark:text-white h-4 stroke-2' />
                    </div>
                </button>

                <div x-show="open" x-collapse.duration.500ms class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 p-1 gap-2 mt-2 flex-wrap">
                    @foreach($category->articles as $article)
                        <a wire:key="article-{{ $article->id }}" class="flex flex-col justify-stretch basis-full md:basis-72 rounded border border-nlrc-blue-200 dark:border-nlrc-blue-950 hover:text-nlrc-blue-600 dark:hover:text-slate-400 hover:ring-1 hover:ring-nlrc-blue-500" href="{{ route('kb.show', ['category' => $category->slug, 'article' => $article->slug]) }}">
                            <p class="bg-nlrc-blue-100 dark:bg-nlrc-blue-900 p-2 border-b border-nlrc-blue-200 dark:border-nlrc-blue-950 font-bold">{{ $article->title }}</p>
                            <p class="p-2">{{ $article->summary }}</p>
                            <p class="p-2 text-nlrc-blue-500 dark:text-sky-600 hover:text-nlrc-blue-600 dark:hover:text-sky-500 text-right italic md:justify-self-end mt-auto">&rarr; click to read more</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </x-page-section>

    <x-page-section>
        <p>If the articles above did not answer your question, email us at <strong>support@nlrc.ph<strong>.</p>
    </x-page-section>
</div>



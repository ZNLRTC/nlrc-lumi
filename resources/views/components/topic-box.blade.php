<div class="py-1 lg:py-2" id="{{ $attributes['id'] }}" x-data="{ topicOpen: false }">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-nlrc-blue-800 overflow-hidden shadow sm:shadow-md sm:rounded-lg">

            <div class="px-4 lg:px-8 bg-white dark:bg-nlrc-blue-800 dark:bg-gradient-to-bl dark:from-slate-700/50 dark:via-transparent">
                
                @if (isset($title))
                    <div class="flex justify-between items-center pt-4 lg:pt-8 cursor-pointer" @click="topicOpen = !topicOpen"  title="Expand and collapse the topic">
                        <h2 class="font-base text-lg text-slate-800 dark:text-slate-100">
                            {{ $title }}
                        </h2>
                        <div :class="{'rotate-180': topicOpen, 'rotate-0': !topicOpen}" class="transition-transform duration-500">
                            <x-heroicon-o-chevron-down class='text-nlrc-blue-500 dark:text-white h-5 stroke-2' />
                        </div>
                    </div>
                @endif

                <div x-show="topicOpen" x-collapse.duration.1000ms>
                    @if (isset($description))
                        <div class='flex flex-col lg:flex-row justify-between items-start lg:items-center'>
                            <p class="mt-0 text-slate-500 text-md dark:text-slate-400 leading-relaxed">
                                {{ $description }}
                            </p>
                            <div class="title-actions flex">
                                @php
                                    $quiz = $this->has_quiz($attributes['topic-id']);
                                @endphp
                                @if(Auth::user()->role->name !== "Trainee")
                                    <!-- If a quiz already exists, update quiz and show quiz will is the one to be displayed -->
                                    @if($quiz)
                                        <a title="View questions" class="btn bg-nlrc-blue-500 dark:bg-nlrc-blue-700" href="/quiz/view?quiz={{Crypt::encrypt($quiz[0]['version_id'])}}&back={{Crypt::encrypt(request()->path())}}">View quiz</a>
                                        <a class="btn bg-nlrc-blue-500 dark:bg-nlrc-blue-700 ml-1" title="Update" href="/quiz/update?quiz={{Crypt::encrypt($quiz[0]['version_id'])}}&back={{Crypt::encrypt(request()->path())}}"> Update quiz</a>
                                    @else
                                        <a class="btn bg-nlrc-blue-500 dark:bg-nlrc-blue-700" href="/quiz/add_quiz?topic={{ $attributes['topic-id'] }}&back={{Crypt::encrypt(request()->path())}}">Create quiz</a>
                                    @endif
                                @else
                                    @if($quiz)
                                        <a class="btn bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="Answer Quiz" href="/quiz/attempt?quiz={{Crypt::encrypt($quiz[0]['version_id'])}}&back={{Crypt::encrypt(request()->path())}}" >Take quiz</a>
                                    @endif
                                @endif
                                <!-- If trainee is logged in, display button for take quiz-->
                                <!-- If quiz is not yet made, dsiplay "No quiz avaialable"-->
                            </div>
                        </div>
                    @endif

                    @if (isset($content))
                        <div class="mt-6 leading-relaxed nlrc text-slate-800 dark:text-slate-200">
                            {!! $content !!}
                        </div>
                    @endif

                    <div class="flex gap-10 justify-between lg:justify-end mt-6 lg:mt-8">
                        <a href="#{{ $attributes['id'] }}" class="flex justify-start items-center gap-2 text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-600 dark:hover:text-sky-500" title="Return to the heading of this topic">
                            <x-heroicon-s-arrow-up class='h-5 stroke-2' />
                            <p>back to top</p>
                        </a>
                        <div class="flex justify-start items-center gap-2 text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-600 dark:hover:text-sky-500 cursor-pointer" @click="topicOpen = !topicOpen" title="Collapse this topic">
                            <x-heroicon-s-chevron-up class='h-5 stroke-2' />
                            <p>collapse topic</p>
                        </div>
                    </div>
                </div>
                
                {{-- Needed to make the bottom margin interactable --}}
                @if (isset($title))
                    <div class="h-4 lg:h-8 cursor-pointer" @click="topicOpen = !topicOpen" title="Expand and collapse the topic"></div>
                @endif

            </div>
        </div>
    </div>
</div>
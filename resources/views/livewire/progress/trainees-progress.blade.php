<div class="max-w-7xl sm:px-6 lg:px-8 dark:text-slate-200 {{ auth()->user()->hasRole('Trainee') ? 'px-4 py-6 mx-auto' : '' }}">
    @if (auth()->user()->hasRole('Trainee'))
        <p class="mb-6">You can track your progress in the training program here. If you are moved to another group, have an employer with certain requirements, or are affected by changes in our curriculum, progress listed here may change. Rest assured though, we have all changes and progress on record even if it is not always displayed here.</p>
    @endif

    <div class="grid grid-flow-row grid-cols-1 gap-y-4 md:gap-x-4 {{ auth()->user()->hasRole('Trainee') ? 'md:grid-cols-2' : '' }}">
        <div class="p-4 rounded w-full border border-nlrc-blue-200 dark:border-nlrc-blue-600 dark:bg-nlrc-blue-800">
            <h2 class="text-xl">Courses</h2>

            {{-- Total courses completion rate --}}
            <x-custom.progress-bar-animated
                :progress_bar_colors="['bg-blue-300', 'dark:bg-blue-400']"
                :text_alignment="'right'"
                livewire_property_to_use="courses_completion"
                class="mt-4 gap-x-2"
            />

            <div class="grid grid-flow-row gap-y-6 gap-x-2 mt-4 progress-courses-container">
                @foreach ($courses as $course)
                    <x-custom.progress-bar-animated
                        :progress_bar_colors="['bg-blue-300', 'dark:bg-blue-400']"
                        :progress_bar_value="$course['course_completion']"
                        :text_alignment="'right'"
                        :use_livewire_property="false"
                        class="gap-x-2"
                    />

                    <h3 class="text-end text-ellipsis overflow-hidden whitespace-nowrap" title="{{ $course['name'] }}">{{ $course['name'] }}</h3>
                @endforeach
            </div>
        </div>

        <div class="p-4 rounded w-full border border-nlrc-blue-200 dark:border-nlrc-blue-600 dark:bg-nlrc-blue-800">
            @php
                $completed_proficiencies_count = count(array_filter($language_proficiencies, function($val) {
                    return $val['is_proficient'] == 1;
                }));

                $completed_proficiencies_percent = round((($completed_proficiencies_count / count($language_proficiencies)) * 100), 2);
            @endphp

            <h2 class="text-xl">Language Proficiency</h2>

            <x-custom.progress-bar-animated
                :progress_bar_colors="['bg-green-300', 'dark:bg-green-400']"
                :text_alignment="'right'"
                :use_livewire_property="false"
                :progress_bar_value="$completed_proficiencies_percent"
                class="mt-4 gap-x-2"
            />

            <div class="flex flex-col gap-y-6 mt-4">
                @foreach ($language_proficiencies as $language_proficiency)
                    <div class="flex flex-row items-center gap-x-4">
                        <div class="rounded-full w-8 h-8 border-2 {{ $language_proficiency['is_proficient'] == 1 ? 'bg-green-300 dark:bg-green-400 border-green-300 dark:border-green-400' : 'bg-nlrc-blue-300 dark:bg-nlrc-blue-400 border-nlrc-blue-300 dark:border-nlrc-blue-400' }} completion-progress-circle"></div>

                        <div class="flex-1">
                            <p class="text-lg">{{ $language_proficiency['proficiency'] }}</p>

                            <p class="text-sm dark:text-slate-400">{{ $language_proficiency['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-4 rounded w-full border border-nlrc-blue-200 dark:border-nlrc-blue-600 dark:bg-nlrc-blue-800">
            <h2 class="text-xl">Document Completion</h2>

            <x-custom.progress-bar-animated
                :progress_bar_colors="['bg-yellow-300', 'dark:bg-yellow-400']"
                :text_alignment="'right'"
                livewire_property_to_use="document_completion"
                class="mt-4 gap-x-2"
            />
        </div>

        <div class="p-4 rounded w-full border border-nlrc-blue-200 dark:border-nlrc-blue-600 dark:bg-nlrc-blue-800">
            <h2 class="text-xl">Profile Completion</h2>

            <x-custom.progress-bar-animated
                :progress_bar_colors="['bg-red-300', 'dark:bg-red-400']"
                :text_alignment="'right'"
                livewire_property_to_use="profile_completion"
                class="mt-4 gap-x-2"
            />
        </div>
    </div>
</div>

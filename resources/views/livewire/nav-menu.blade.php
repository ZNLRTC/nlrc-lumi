<nav x-data="{ open: false }" class="container-fluid bg-nlrc-blue-500 dark:bg-nlrc-blue-900 border-b-2 border-nlrc-blue-500 dark:border-nlrc-blue-700">
    <!-- Primary Navigation Menu -->
    <div class="container">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a wire:navigate href="{{ route('dashboard') }}" id="logo">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link wire:navigate href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <div class="hidden sm:flex sm:items-center cursor-pointer">
                        <x-dropdown align="left">
                            <x-slot name="trigger">
                                <x-nav-link :active="request()->routeIs('courses.index')" class="h-[66px]">
                                    Courses
                                </x-nav-link>
                            </x-slot>

                            <x-slot name="content">
                                {{-- Every trainee has access to this so it's always shown --}}
                                <x-dropdown-link wire:navigate href="{{ route('courses.kmh') }}">
                                    <span class="italic">Kyl mä hoidan</span> beginners' course
                                </x-dropdown-link>

                                {{-- $courses is handled by a view composer in AppServiceProvider --}}
                                @foreach ($courses as $course)
                                    <x-dropdown-link wire:navigate href="{{ route('courses.index', $course['slug'] ? $course['slug'] : $course['id'])}}" :active="request()->routeIs('courses.index', $course['slug'] ? $course['slug'] : $course['id'])">
                                        {{ $course['name'] }}
                                    </x-dropdown-link>
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    </div>

                    {{-- Role names need a capital letter --}}
                    @if (Auth::user()->hasRole('Trainee'))
                        <x-nav-link wire:navigate href="{{ route('meetings.index') }}" :active="request()->routeIs('meetings.index')">
                            Meetings
                        </x-nav-link>
                        <x-nav-link wire:navigate href="{{ route('showDocumentUpload') }}" :active="request()->routeIs('showDocumentUpload')">
                            Upload documents
                        </x-nav-link>
                    @endif

                    {{-- I hid this in the live staging version since real-life instructors are testing it now and they wouldn't access this normally --}}
                    @if (Auth::user()->hasAnyRole(['Admin','Manager','Staff','Editing instructor']))
                        <x-nav-link wire:navigate href="/quiz" :active="request()->routeIs('/quiz')">
                            {{ __('Quizzes') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->hasAnyRole(['Instructor','Editing instructor']))
                        <x-nav-link wire:navigate href="{{ route('meetings.create') }}" :active="request()->routeIs('meetings.create')">
                            New meeting
                        </x-nav-link>
                        <x-nav-link wire:navigate href="{{ route('exams.index') }}" :active="request()->routeIs('exams.index')">
                            Exams and assessments
                        </x-nav-link>
                    @endif

                    <x-nav-link wire:navigate href="{{ route('kb.index') }}" :active="request()->routeIs('kb.index') || request()->routeIs('kb.show')">
                        Help
                    </x-nav-link>

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-custom.notification-bell
                    :trainee_notifications="$trainee_notifications"
                    :trainee_notifications_unread_count="$trainee_notifications_unread_count"
                    :trainee_notifications_unread_count_is_overlap="$trainee_notifications_unread_count_is_overlap"
                />

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-nlrc-blue-300 transition">
                                <img src="{{ auth()->user()->websitePhotoUrl() }}" class="h-10 w-10 rounded-full object-cover" alt="User website photo" title="User website photo" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- Dark mode toggle --}}
                            <div class="block px-4 pt-2 text-xs text-slate-400">
                                Dark mode
                            </div>

                            <div x-data="window.themeSwitcher()" x-init="init" @keydown.window.tab="switchOn = false" class="flex place-content-start px-2 py-2 space-x-2">
                                <input id="thisId" type="checkbox" name="switch" class="hidden" :checked="switchOn">

                                <button
                                    x-ref="switchButton"
                                    type="button"
                                    @click="switchOn = ! switchOn; switchTheme()"
                                    :class="switchOn ? 'bg-nlrc-blue-500' : 'bg-nlrc-blue-400'"
                                    class="relative inline-flex h-5 py-0.5 ml-2 focus:outline-none rounded-full w-9">
                                    <span :class="switchOn ? 'translate-x-[18px]' : 'translate-x-0.5'" class="w-4 h-4 duration-200 ease-in-out bg-white rounded-full shadow-md"></span>
                                </button>

                                <label @click="$refs.switchButton.click(); $refs.switchButton.focus()" :id="$id('switch')"
                                    :class="{ 'text-slate-300': switchOn, 'text-slate-700': ! switchOn }"
                                    class="text-sm select-none">
                                    On
                                </label>
                            </div>

                            <div class="border-t border-nlrc-blue-100 dark:border-nlrc-blue-600"></div>

                            @if (Auth::user()->hasAnyRole(['Admin', 'Manager', 'Staff']))
                                <x-dropdown-link wire:navigate href="{{ route('filament.admin.pages.dashboard') }}">Administration</x-dropdown-link>

                                <div class="border-t border-nlrc-blue-100 dark:border-nlrc-blue-600"></div>
                            @endif

                            @if (Auth::user()->hasRole('Trainee'))
                                <x-dropdown-link wire:navigate href="{{ route('announcements.index') }}">Announcements</x-dropdown-link>

                                <x-dropdown-link wire:navigate href="{{ route('progress.index') }}">Your progress</x-dropdown-link>

                                <div class="border-t border-nlrc-blue-100 dark:border-nlrc-blue-600"></div>
                            @endif

                            <!-- Account Management -->
                            <div class="block px-4 pt-2 text-xs text-slate-400">
                                Manage account
                            </div>

                            <x-dropdown-link wire:navigate href="{{ route('profile.show') }}">Edit your profile</x-dropdown-link>

                            <div class="border-t border-nlrc-blue-100 dark:border-nlrc-blue-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    Log out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <x-custom.notification-bell
                    :trainee_notifications="$trainee_notifications"
                    :trainee_notifications_unread_count="$trainee_notifications_unread_count"
                    :trainee_notifications_unread_count_is_overlap="$trainee_notifications_unread_count_is_overlap"
                />

                <button @click="open = ! open" x-bind:class="{ 'focus: rotate-90': open }" class="inline-flex items-center justify-center p-2 rounded-md text-slate-200 dark:text-slate-500 hover:text-slate-50 dark:hover:text-slate-50 focus:outline-none focus:text-slate-100 dark:focus:text-slate-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" x-show="open" x-collapse.duration.500ms class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link wire:navigate href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <x-heroicon-s-home class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                <p>Dashboard</p>
            </x-responsive-nav-link>

            @if (Auth::user()->hasAnyRole(['Admin', 'Manager', 'Staff']))
                <x-responsive-nav-link wire:navigate href="{{ route('filament.admin.pages.dashboard') }}">
                    <x-heroicon-s-presentation-chart-bar class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>Administration</p>
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->hasAnyRole(['Instructor','Editing instructor']))
                <x-responsive-nav-link wire:navigate href="{{ route('meetings.create') }}" :active="request()->routeIs('meetings.create')">
                    <x-heroicon-s-chat-bubble-bottom-center-text class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>New meeting</p>
                </x-responsive-nav-link>

                <x-responsive-nav-link wire:navigate href="{{ route('exams.index') }}" :active="request()->routeIs('exams.index')">
                    <x-heroicon-s-document-text class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>Exams and assessments</p>
                </x-responsive-nav-link>
            @endif

            <div class="pt-2 pb-1 border-b border-nlrc-blue-600 dark:border-nlrc-blue-600">
                <div class="ps-4 py-2 space-y-1 text-sm text-white dark:text-slate-300 tiny-heading bg-nlrc-blue-600/50 dark:bg-nlrc-blue-800">Courses</div>
                    {{-- Every trainee has access to this so it's always shown --}}
                    <x-responsive-nav-link wire:navigate href="{{ route('courses.kmh') }}" :active="request()->routeIs('courses.kmh')">
                        <x-heroicon-o-book-open class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                        <p><span class="italic">Kyl mä hoidan</span> beginners' course</p>
                    </x-responsive-nav-link>

                    {{-- $courses is handled by a view composer in AppServiceProvider --}}
                    @foreach ($courses as $course)
                        <x-responsive-nav-link wire:navigate href="{{ route('courses.index', $course['slug'] ? $course['slug'] : $course['id']) }}" :active="request()->routeIs('courses.index', $course['slug'] ? $course['slug'] : $course['id'])">
                            <x-heroicon-s-book-open class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                            <p>{{ $course['name'] }}</p>
                        </x-responsive-nav-link>
                    @endforeach
            </div>

            @if (Auth::user()->hasRole('Trainee'))
                <x-responsive-nav-link wire:navigate href="{{ route('meetings.index') }}" :active="request()->routeIs('meetings.index')">
                    <x-heroicon-s-chat-bubble-bottom-center-text class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>Meetings</p>
                </x-responsive-nav-link>

                <x-responsive-nav-link wire:navigate href="{{ route('announcements.index') }}" :active="request()->routeIs('announcements.index')">
                    <x-heroicon-s-megaphone class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>Announcements</p>
                </x-responsive-nav-link>

                <x-responsive-nav-link wire:navigate href="{{ route('showDocumentUpload') }}" :active="request()->routeIs('showDocumentUpload')">
                    <x-heroicon-s-arrow-up-on-square-stack class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>Upload documents</p>
                </x-responsive-nav-link>

                <x-responsive-nav-link wire:navigate href="{{ route('progress.index') }}" :active="request()->routeIs('progress.index')">
                    <x-heroicon-s-trophy class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>Your progress</p>
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link wire:navigate href="{{ route('kb.index') }}" :active="request()->routeIs('kb.index') || request()->routeIs('kb.show')">
                <x-heroicon-s-question-mark-circle class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                <p>Help</p>
            </x-responsive-nav-link>

        </div>

        {{-- Responsive menu dark mode toggle --}}
        <div class="pb-1">
            <div class="ps-4 py-2 space-y-1 tiny-heading text-white dark:text-slate-300 bg-nlrc-blue-600/50 dark:bg-nlrc-blue-800">Dark mode</div>

            <div x-data="window.themeSwitcher()" x-init="init" @keydown.window.tab="switchOn = false" class="flex place-content-start mt-1 ps-4 py-2 space-x-2">
                <x-heroicon-s-moon class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                <input id="thisId" type="checkbox" name="switch" class="hidden" :checked="switchOn">

                <button
                    x-ref="switchButton"
                    type="button"
                    @click="switchOn = ! switchOn; switchTheme()"
                    :class="switchOn ? 'bg-nlrc-blue-600' : 'bg-nlrc-blue-200'"
                    class="relative inline-flex h-6 py-0.5 ml-2 focus:outline-none rounded-full w-10">
                    <span :class="switchOn ? 'translate-x-[18px]' : 'translate-x-0.5'" class="w-5 h-5 duration-200 ease-in-out bg-nlrc-blue-600 dark:bg-nlrc-blue-200 rounded-full shadow-md"></span>
                </button>

                <label @click="$refs.switchButton.click(); $refs.switchButton.focus()" :id="$id('switch')"
                    :class="{ 'text-slate-400': switchOn, 'text-white': ! switchOn }"
                    class="select-none">
                    On
                </label>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pb-1">
            <div class="block px-4 py-2 tiny-heading text-white dark:text-slate-300 bg-nlrc-blue-600/50 dark:bg-nlrc-blue-800">
                Manage account
            </div>

            <div class="flex items-center px-4 py-3 bg-nlrc-blue-600/30 dark:bg-nlrc-blue-800/50">
                <div class="shrink-0 me-4">
                    <img src="{{ auth()->user()->websitePhotoUrl() }}" class="h-12 w-12 rounded-full object-cover" alt="User website photo" title="User website photo" />
                </div>

                <div>
                    <div class="font-medium text-sm text-white dark:text-slate-300">logged in as:</div>
                    <div class="font-medium text-lg text-white dark:text-slate-300">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-white dark:text-slate-300">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-2 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link wire:navigate href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    <x-heroicon-s-pencil class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                    <p>Edit your profile</p>
                </x-responsive-nav-link>

                {{-- @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif --}}

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        <x-heroicon-s-arrow-left-start-on-rectangle class='h-5 w-auto text-sky-300 dark:text-sky-400'/>
                        <p>Log out</p>
                    </x-responsive-nav-link>
                </form>

            </div>
        </div>
    </div>
</nav>

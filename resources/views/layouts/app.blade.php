<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ZNLRTC Lumi') }}</title>
        
        <!-- The below are for SEO purposes -->
        <meta name="keywords" content="Zeldan Nordic Language Review and Training Center" />
        <meta property="og:title" content="Zeldan Nordic Language Review and Training Center" />
        <meta name="og:description" content="Zeldan Nordic Language Review and Training Center" />

        <!-- Force the browser to load the latest version of pages each time it is accessed -->
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />

        <!-- This gets rid of white flashes at page load if the dark mode is on -->
        <script>
            if (JSON.parse(localStorage.getItem('isDark'))) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,400i,700,700i,900,900i&display=swap" rel="stylesheet" />
        
        <!-- Styles -->
        @vite(['resources/css/app.css'])

        <!-- Custom styles for pages -->
        @yield('custom_stylesheet')

        @livewireStyles
        @stack('custom-styles')
        
        <script>
            var base_url = "{{url('/')}}/";
        </script>
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-nlrc-blue-50/50 dark:bg-nlrc-blue-900">
            <livewire:NavMenu />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-nlrc-blue-50 dark:bg-nlrc-blue-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <!-- Modals -->
        
        @stack('modals')
        @livewireScripts
        @stack('custom-scripts')
        
        <!-- Scripts -->
        <!-- Transferred the scripts here for faster loading - Joe -->
        @vite(['resources/js/app.js'])

        <!-- custom scripts for pages -->
        @yield('custom_script')
    </body>
</html>

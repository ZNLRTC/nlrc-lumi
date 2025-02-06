<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="application-name" content="{{ config('app.name') }}" />
        <title>{{ config('app.name', 'NLRC Lumi') }}</title>

        {{-- The below are for SEO purposes --}}
        <meta name="keywords" content="Zeldan Nordic Language Review and Training Center" />
        <meta property="og:title" content="Zeldan Nordic Language Review and Training Center" />
        <meta name="og:description" content="Zeldan Nordic Language Review and Training Center" />

        {{-- Force the browser to load the latest version of pages each time it is accessed --}}
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />

        {{-- This gets rid of white flashes at page load if the dark mode is on --}}
        <script>
            if (JSON.parse(localStorage.getItem('isDark'))) {
                document.documentElement.classList.add('dark');
            }
        </script>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,400i,700,700i,900,900i&display=swap" rel="stylesheet" />

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles
        @livewireStyles
        @vite('resources/css/app.css')
    </head>

    <body x-data="themeSwitcher()" :class="{ 'dark': switchOn }" class="font-sans antialiased">
        {{ $slot }}

        @filamentScripts
        @livewireScripts
        @vite('resources/js/app.js')
    </body>
</html>

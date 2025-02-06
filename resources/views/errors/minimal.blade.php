<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>@yield('title')</title>
        @vite(['resources/css/app.css'])

    </head>
    <body class="antialiased">
        <div class="relative flex items-top sm:items-center justify-center min-h-screen bg-nlrc-blue-600 dark:bg-nlrc-blue-700 sm:pt-0">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-start pt-8 sm:pt-0">
                    <div class="rounded">
                        <img src="{{ asset('img/errors/404-1.jpeg') }}" width="150" height="150" alt="Cat snoozing and totally ignoring this error message" class="w-32 sm:w-44 rounded-lg saturate-50">
                    </div>

                    <div class="ms-2 sm:ms-4 mt-2 sm:mt-6 text-slate-200 tracking-wider flex flex-col items-center relative">
                        <p class="mb-0 sm:mb-1">error @yield('code'):</p>
                        <p class="text-xl uppercase">@yield('message')</p>
                        <p class="text-sm sm:text-base mt-2 sm:mt-4 ms-1 sm:ms-2 rotate-3 tracking-normal text-slate-300">Here's a cat instead.</p>
                        <svg class="absolute -translate-x-1/2 start-[40%] -bottom-9 rotate-12 stroke-1 stroke-slate-400 fill-none w-12 h-12" width="50" height="50" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6a13.17 13.17 0 0 1-12.49 9H3" style="stroke-linecap:round;stroke-linejoin:round;"/>
                            <path style="stroke-linecap:round;stroke-linejoin:round;" d="m6 12-3 3 3 3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

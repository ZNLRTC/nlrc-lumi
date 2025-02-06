<div class="flex gap-4">
    <x-application-logo class="block h-12 w-auto flex-none" />

    <div class="flex flex-col">
        <h1 class="font-medium text-xl text-slate-900 dark:text-white">
            Welcome to Lumi, NLRC's online learning platform!
        </h1>

        <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
            @if (Auth::user()->hasRole('Trainee'))
                Access materials, submit weekly tasks, track your progress, and upload your documents here.
            @elseif (Auth::user()->hasRole('Instructor'))
                Grade meetings and exams and create on-call meetings here.
            @endif
        </p>
    </div>
</div>

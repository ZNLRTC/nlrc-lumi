<div>
    <h2 class="flex items-center gap-2 mb-2 text-xl dark:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
            <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054A8.25 8.25 0 0 0 18 4.524l3.11-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
        </svg>
        <span>Application Status History</span>
    </h2>

    @if (count($trainee_flags) > 0)
        <ul class="mt-6 space-y-2 md:space-y-6">
            @foreach ($trainee_flags as $flag)
                <li class="dark:text-slate-400">
                    <span class="tracking-wider font-bold dark:text-white">{{ $flag->flag->name }}</span>
                    <ol class="ml-4">
                        <li class="flex gap-3">Flagged on: <span class="bold dark:text-white">{{ \Carbon\Carbon::parse($flag->created_at->format('Y-m-d h:i:s A'), 'UTC')->setTimezone(Auth::user()->timezone)->format('M j, Y g:i A') }}</span></li>

                        @if ($flag->description)
                            <li class="flex gap-3">Remarks: <span class="dark:text-white">{{ $flag->description }}</span></li>
                        @endif
                    </ol>
                </li>
            @endforeach
        </ul>
    @else
        <p class="leading-relaxed text-slate-500 dark:text-slate-400">You haven't been flagged yet.</p>
    @endif
</div>

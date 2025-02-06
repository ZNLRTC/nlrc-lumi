<p class='my-2'>{{ $meeting->description }}</p>

<ul class='grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3'>

@forelse ($meeting->trainees as $trainee)

    @include('meetings.partials.individual-meeting', ['trainee' => $trainee])

@empty

    <li class='block border p-2 text-sm rounded bg-nlrc-blue-50 border-nlrc-blue-100 dark:border-nlrc-blue-600'>
        <p class='text-slate-600 dark:text-slate-300'>No recorded attempts yet.</p>
    </li>

@endforelse

</ul>
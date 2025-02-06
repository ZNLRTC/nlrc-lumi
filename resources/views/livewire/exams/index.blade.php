<x-page-section>
    <h1>You can assign grades in these tests, exams, and assessments:</h1>
    @foreach($gradableExams as $type => $exams)
        <h2 class='text-xl mt-4'>{{ ucfirst(Str::plural($type)) }}</h2>
        <ul>
            @foreach($exams as $exam)
                <li>
                    <a class="text-nlrc-blue-500 hover:text-nlrc-blue-600 dark:text-sky-500 dark:hover:text-sky-300" href="{{ route('exams.show', ['type' => strtolower($type), 'exam' => $exam->id]) }}">
                        {{ $exam->name }}
                    </a>
                    @if($exam->date)
                        on {{ \Carbon\Carbon::parse($exam->date)->format('D, M j, Y') }}
                    @endif
                </li>
            @endforeach
        </ul>
    @endforeach
</x-page-section>

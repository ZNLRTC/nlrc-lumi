@php
    use App\Enums\Exams\ExamAttemptStatus;
@endphp

<x-page-section>
    @if ($groupedExamAttempts && count($groupedExamAttempts) > 0)
        @foreach ($groupedExamAttempts as $type => $attempts)
            <h3 class='text-lg border-b border-nlrc-blue-200 dark:border-nlrc-blue-900 mt-2 first:mt-0'>{{ ucfirst(Str::plural($type)) }}</h3>
            <div class='w-full grid grid-cols-1 md:grid-cols-2 gap-4'>
                @foreach ($attempts as $attempt)

                    @php
                        $statusClass = '';
                        switch ($attempt->status) {
                            case ExamAttemptStatus::PASSED:
                                $statusClassBorder = 'border-green-200 dark:border-green-900';
                                $statusClassBg = 'bg-green-100 dark:bg-green-900';
                                $statusClassBgBase = 'dark:bg-green-700';
                                break;
                            case ExamAttemptStatus::FAILED:
                                $statusClassBorder = 'border-red-200 dark:border-red-950';
                                $statusClassBg = 'bg-red-100 dark:bg-red-950';
                                $statusClassBgBase = 'dark:bg-red-900';
                                break;
                            case ExamAttemptStatus::ABSENT:
                                $statusClassBorder = 'border-nlrc-blue-200 dark:border-nlrc-blue-900';
                                $statusClassBg = 'bg-nlrc-blue-100 dark:bg-nlrc-blue-900';
                                $statusClassBgBase = '';
                                break;
                            default:
                                $statusClassBorder = 'border-nlrc-blue-200 dark:border-nlrc-blue-900';
                                $statusClassBg = 'bg-nlrc-blue-100 dark:bg-nlrc-blue-900';
                                $statusClassBgBase = '';
                                break;
                        }
                    @endphp

                    <div class='my-4 flex flex-col rounded border {{ $statusClassBorder }}'>

                        {{-- Heading --}}
                        <div class='p-2 lg:p-4 flex flex-col lg:flex-row justify-between {{ $statusClassBg }}'>
                            <div class='font-semibold'>
                                {{ $attempt->exam->name }}
                            </div>
                            <div>
                                {{ $attempt->date ? \Carbon\Carbon::parse($attempt->exam->date)->inUserTimezone()->format('D, M j, Y') : '' }}
                            </div>
                        </div>

                        {{-- Result announcement --}}
                        <div class='p-2 lg:p-4 {{ $statusClassBgBase }} flex gap-2 md:gap-4 items-start md:items-center'>
                            @switch($attempt->status)
                                @case(ExamAttemptStatus::PASSED)
                                    <x-heroicon-o-trophy class="w-8 text-amber-400 dark:text-amber-500 shrink-0" />
                                    <p>Congratulations! You demonstrated the skills of the tested proficiency level in this {{ $attempt->exam->type }} (i.e. you passed).</p>
                                    @break

                                @case(ExamAttemptStatus::FAILED)
                                    <p>Unfortunately, you did not demonstrate the required proficiency in this {{ $attempt->exam->type }} and must retake it on a later date.</p>
                                    @break

                                @case(ExamAttemptStatus::ABSENT)
                                    <p>You did not attend the scheduled {{ $attempt->exam->type }}.</p>
                                    @break

                                @default
                                    <p>Your result was erroneously released without a conclusion as to whether you reached the required proficiency or not. Contact NLRC staff.</p>
                            @endswitch
                        
                        </div>

                        {{-- Feedback or scores--}}
                        <div class="p-2 lg:p-4 border-t {{ $statusClassBorder }} {{ $statusClassBgBase }}">
                            
                            @if ($attempt->exam->type !== 'exam' )
                                @if ($attempt->feedback)
                                    <p>Feedback from the instructor:<br><span class='italic'>{{ $attempt->feedback }}</span></p>
                                @else
                                    <p>The instructor did not leave you feedback at this time.</p>
                                @endif
                                
                            @elseif (in_array($attempt->status, [ExamAttemptStatus::PASSED, ExamAttemptStatus::FAILED, ExamAttemptStatus::ABSENT]))
                                @php
                                    $sectionScores = $this->getSectionScores($attempt);
                                    // dd($sectionScores);
                                @endphp

                                @foreach ($sectionScores as $sectionScore)

                                    @php
                                        $percentage = ($sectionScore['total_score'] / $sectionScore['total_max_score']) * 100;
                                    @endphp
                            
                                    <div class='flex flex-row gap-2'>
                                        <div>
                                            @if ($percentage >= $sectionScore['section']->passing_percentage)
                                                <x-heroicon-s-check-circle class='h-5 inline text-nlrc-green-100 dark:text-green-500' />
                                            @else
                                                <x-heroicon-s-x-circle class='h-5 inline text-red-400 dark:text-red-500' />
                                            @endif
                                        </div>
                                        <div>
                                            <div class='flex flex-col sm:flex-row gap-0 sm:gap-1'>
                                                <p>{{ ucfirst($sectionScore['section']->short_name) }}: {{ $sectionScore['total_score'] }} / {{ $sectionScore['total_max_score'] }}</p>
                                                <p>({{ number_format($sectionScore['total_score'] / $sectionScore['total_max_score'] * 100, 1) }}%, {{ rtrim(rtrim(number_format(number_format($sectionScore['section']->passing_percentage), 1), '0'), '.') }}% needed)</p>
                                            </div>
                                            @if ($sectionScore['section']->short_name === 'writing')
                                                <table class='ms-4 w-full'>
                                                    @foreach ($sectionScore['tasks'] as $task)
                                                        <tr>
                                                            <td>{{ ucfirst($task['task_name']) }}</td>
                                                            <td class='ps-2'>{{ $task['score'] }} / {{ $task['max_score'] }}</td>
                                                        </tr>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <p>Your results in any tests, assessments, or exams have not been published yet.</p>
    @endif
</x-page-section>

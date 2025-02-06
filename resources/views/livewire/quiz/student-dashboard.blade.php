<div class="container">   
    <div class="row table-row">
        <table id="quiz-table">
            <thead class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700">
                <tr class="search-row">
                    <th colspan="5">
                        <div>
                            <input type="text" name="search" placeholder="search here"
                            wire:model="search">
                            <button type="button" title="search" wire:click="fetch_quizzes()">@include('livewire/quiz/svg/search')</button>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th scope="col">QUIZ TITLE</th>
                    <th scope="col" class="description">DESCRIPTION</th>
                    <th scope="col" class="attempts">ATTEMPT/S</th>
                    <th scope="col">AVERAGE</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @if(!empty($this->quiz) || $this->quiz !== null)
                    @foreach($quiz as $index => $q)
                        <?php $latest = $this->quiz_latest($q['id']); ?>
                        <tr>
                            <td class="title-column">
                                <b>{{$latest['title']}} v.{{$latest['version_number']}}</b>
                                <span title="translation">
                                    @include('livewire/quiz/svg/refresh')
                                    <i>{{$latest['title-translation']}}</i>
                                </span>
                            </td>
                            <td class="description-column description">
                                {{$latest['description']}}
                                <span title="translation">
                                    @include('livewire/quiz/svg/refresh')
                                    <i>{{$latest['description-translation']}}</i>
                                </span>
                            </td>
                            <td class="attempt-column attempts">
                                <?php
                                    $quiz_versions  = $this->get_versions_list($q['id']);
                                    $attempts_num   = 0;
                                    $score          = 0;
                                    $questionnaires = 0;
                                    
                                    foreach($quiz_versions as $key => $quiz_version):
                                        $attempts_num += count($this->get_trainee_attempts($quiz_version['id'], Auth::user()->id));
                                        $attempts = $this->get_trainee_attempts($quiz_version['id'], Auth::user()->id);
                                        foreach($attempts as $attempt):
                                            $questionnaires += count($this->questionnaires($attempt['quiz_version_id']));
                                            $score          += $attempt['score'];
                                        endforeach;
                                    endforeach;
                                    echo $attempts_num;
                                ?>
                            </td>
                            <td class="score-column">
                                @if( $questionnaires > 0 )
                                    <?php
                                        $percentage = round((($score / $questionnaires) * 100), 2);
                                        $rate = $this->rate($percentage);
                                    ?>
                                    {{$percentage}}%
                                    <span class="text-{{$rate}}">{{ucfirst($rate)}}</span>
                                @endif
                            </td>
                            <td class="action-td">
                                <div>
                                    <div class="button-group">
                                        @if($q['archive'] == false)
                                            <a class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="Answer Quiz" href="/quiz/attempt?quiz={{Crypt::encrypt($q['version_id'])}}" >@include('livewire/quiz/svg/pen')&nbsp;&nbsp;Quiz</a>
                                        @endif
                                        @if($attempts_num !== 0)
                                            <a class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="View All Attempts" href="/quiz/view_all_attempts?quiz={{Crypt::encrypt($q['id'])}}">@include('livewire/quiz/svg/eye')&nbsp;&nbsp;Attempts</a>
                                        @endif
                                    </div>
                                    <span class="note {{!$q['archive'] ? 'active' : 'inactive'}}">
                                        {{$q['archive'] ? 'Status: Quiz unavailable...' : ''}}
                                    </span>
                                </div>
                                @if($attempts_num > 0)
                                    <button class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="Attempt list"
                                    wire:click.prevent="toggle_versions({{$q['id']}})"
                                    >
                                        @include('livewire.quiz.svg.angle-' . (($q["expanded"] == false) ? 'down' : 'up'))
                                    </button>
                                @endif
                            </td>
                        </tr>
                        <!-- Display only the attempted list here -->
                        @foreach($this->quiz_versions as $quiz_version)
                            @if($quiz_version['quiz_id'] == $q['id'] && $this->get_trainee_attempts($quiz_version['id'], Auth::user()->id))
                                <tr class="{{ $quiz_version['hidden'] ? 'hidden' : '' }} sub-tr">
                                    <td class="title-column d-flex">
                                        @include('livewire/quiz/svg/right-long')
                                        <div>
                                            <b>{{ $quiz_version['title'] }} v.{{ $quiz_version['version_number'] }}</b>
                                            <span title="translation">
                                                @include('livewire/quiz/svg/refresh')
                                                <i>{{ $quiz_version['title-translation'] }}</i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="description"></td>
                                    <td class="attempt-column">
                                        <?php
                                            $attempts = $this->get_trainee_attempts($quiz_version['id'], Auth::user()->id);
                                            $count = count($attempts) ?? 0;
                                            echo $count;
                                        ?>
                                    </td>
                                    <td class="score-column">
                                        <?php
                                            $score          = 0;
                                            $questionnaires = 0;
                                            foreach($attempts as $attempt):
                                                $questionnaires += count($this->questionnaires($attempt['quiz_version_id']));
                                                $score          += $attempt['score'];
                                            endforeach;
                                            $percentage = round((($score / $questionnaires) * 100), 2);
                                            $rate = $rate = $this->rate($percentage);
                                        ?>
                                        {{$percentage}}%
                                        <span class="text-{{$rate}}">{{ucfirst($rate)}}</span>
                                    </td>
                                    <td class="action-td">
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center text-gray"><i>no quiz available or active</i></td>
                    <tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
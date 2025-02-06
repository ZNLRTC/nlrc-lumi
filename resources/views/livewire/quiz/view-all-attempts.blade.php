<div class="container-fluid" id="quiz-form-body">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 quiz-header">
                <h2><a href="/quiz" title="Go Back" class="t-dblue">
                @include('livewire/quiz/svg/angle-left')
                </a></h2>
                <h2 class="dblue">
                    @include('livewire/quiz/svg/eye')
                    @if(request()->quiz)
                        All Attempts
                    @else
                        {{$this->all_attempts[0]['quiz_version']['title']}} v.{{$this->all_attempts[0]['quiz_version']['version_number']}} Attempts
                    @endif
                </h2>
            </div>

            <div class="col-md-8 offset-md-2 ">
                <table id="quiz-table">
                    <thead class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700">
                        <tr>
                            @if($this->role !== "Trainee")
                                <th>TRAINEE</th>
                            @endif
                            @if(request()->quiz)
                                <th>TITLE</th>
                            @endif
                            <th>DATE TAKEN</th>
                            <th>SCORE</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($this->all_attempts as $attempts)
                            <tr>
                                @if($this->role !== "Trainee")
                                    <td>{{$attempts['trainee']['first_name']}} {{$attempts['trainee']['last_name']}}</td>
                                @endif
                                @if(request()->quiz)
                                    <td class="title-column">
                                        {{$attempts['quiz_version']['title']}}
                                        <span>version: {{$attempts['quiz_version']['version_number']}}</span>
                                    </td>
                                @endif
                                <td class="score-column">
                                    {{(new DateTime($attempts['created_at']))->format('M d, Y')}}
                                    <span>{{(new DateTime($attempts['created_at']))->format('h:i A')}}</span>
                                </td>
                                <td class="score-column">
                                    {{$attempts['score']}}/{{$this->count_questionnaires($attempts['quiz_version_id'])}}
                                    <?php
                                        $rate = $this->rate((($attempts['score']/$this->count_questionnaires($attempts['quiz_version_id']))*100));
                                    ?>
                                    <span class="text-{{$rate}}">{{ucfirst($rate)}}</span>
                                </td>
                                <td class="action-td">
                                    <a class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="View Results" href="/quiz/score?attempt={{Crypt::encrypt($attempts['id'])}}&{{request()->quiz ? 'q='.request()->quiz : 'qv='.request()->qv}}">@include('livewire/quiz/svg/eye')&nbsp;&nbsp;Result</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
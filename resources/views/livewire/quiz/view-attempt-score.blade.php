<div class="container-fluid" id="quiz-form-body">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 quiz-header">
                <?php
                    $url = "quiz";
                    if(request()->q):
                        $url = "quiz/view_all_attempts?quiz=".request()->q;
                    elseif(request()->qv):
                        $url = "quiz/view_all_attempts?qv=".request()->qv;
                    endif;
                ?>
                <h2><a href="/{{$url}}" title="Go Back" class="t-dblue">
                @include('livewire/quiz/svg/angle-left')
                </a></h2>
                <h2 class="dblue">
                    {{$attempt['quiz_version']['title']}} Score
                </h2>
            </div>
            <div class="col-md-6 offset-md-3" id="quiz_form" remove-hover></form>
                <div class="score-div {{$this->commentaries[0]}}">
                    <score>{{$this->attempt['score']}}/{{count($this->questionnaires)}}</score>
                    <h2>{{ucfirst($this->commentaries[0])}}</h2>
                    <span>"{{$this->commentaries[1]}}"</span>
                </div>
                <div class="form-group title-div">
                    <div>
                        {{$attempt['quiz_version']['title-translation']}}
                        <span>(Title Translation)</span>
                    </div>
                    <div>
                        {{$attempt['quiz_version']['description']}}
                        <span>(Description)</span>
                    </div>
                    <div>
                        {{$attempt['quiz_version']['description-translation']}}
                        <span>(Translation)</span>
                    </div>
                </div>
                @foreach($questionnaires as $index => $question)
                    <?php
                        $question_type = $question['latest_version']['question_type'];
                        $q_check       = "question-".$question['version_id'];
                        $wrong_answer  = null;
                        foreach($this->wrong_answers as $wrong):
                            if($wrong == $q_check):
                                $wrong_answer = "border-red";
                                break;
                            endif;
                        endforeach;
                    ?>
                    <div class="form-group question-form {{$wrong_answer}}">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="question">
                                    {{$question['version_id']}}
                                    {{$question['sort_number'].". ".$question['latest_version']['question']}}
                                </div>
                            </div>
                            @if($question_type == "multiple-choice")
                                <div class="col-md-10 multiple-choice-div" answer-div>
                                    @foreach($this->options as $key => $option)
                                        @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                            <div class="option-con">
                                                <input type="radio"
                                                {{$this->answers[$option['quiz_questionnaire_version_id']]['answer'][0] == $this->options[$key]['id'] ? 'checked' : ''}}
                                                disabled>
                                                <div class="option">
                                                    {{$this->options[$key]['option']}}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif($question_type == "check-box")
                                <div class="col-md-10 checkbox-div" answer-div>
                                    @foreach($this->options as $key => $option)
                                        @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                            <?php 
                                                $check = null;
                                                foreach($this->answers[$option['quiz_questionnaire_version_id']]['answer'] as $count => $answer):
                                                    if($this->options[$key]['id'] == $answer):
                                                        $check = 'checked';
                                                        break;
                                                    endif;
                                                endforeach;
                                            ?>
                                            <div class="option-con">
                                                <input type="checkbox"
                                                {{$check}}
                                                disabled>
                                                <div class="option">
                                                    {{$this->options[$key]['option']}}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif($question_type == "boolean")
                                <div class="col-md-10 bool" answer-div>
                                    <div class="option-con">
                                        <input type="radio" value="1"
                                        {{$this->answers[$question['latest_version']['id']]['answer'][0] == 1 ? 'checked' : ''}}
                                        disabled>
                                        <label for="label-{{$index}}-true">True</label>
                                    </div>
                                    <div class="option-con">
                                        <input type="radio" value="0"
                                        {{$this->answers[$question['latest_version']['id']]['answer'][0] == 0 ? 'checked' : ''}}
                                        disabled>
                                        <label for="label-{{$index}}-false">False</label>
                                    </div>
                                </div>
                            @elseif($question_type == "written")
                                <div class="col-md-10 written-div" answer-div>
                                    <div class="option-con">
                                        {{$this->answers[$question['latest_version']['id']]['answer'][0]}}
                                    </div>
                                </div>
                            @endif

                            <div class="explanation">
                                {{$this->questionnaires[$index]['latest_version']['explanation']}}
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>
    </div>
</div>
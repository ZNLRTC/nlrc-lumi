<div class="container-fluid" id="quiz-form-body">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 quiz-header">
                <h2><a href="/{{request()->back ? Crypt::decrypt(request()->back) : 'quiz'}}" title="Go Back" class="t-dblue">
                @include('livewire/quiz/svg/angle-left')
                </a></h2>
                <h2 class="dblue">
                    @include('livewire/quiz/svg/eye')
                    {{$quiz[0]['title']}}
                </h2>
            </div>

            <div class="col-md-6 offset-md-3" id="quiz_form">
                <!-- Make sure this will update -->
                <div class="form-group title-div">
                    <div>
                        {{$quiz[0]['title-translation']}}
                        <span>(Title Translation)</span>
                    </div>
                    <div>
                        {{$quiz[0]['description']}}
                        <span>(Description)</span>
                    </div>
                    <div>
                        {{$quiz[0]['description-translation']}}
                        <span>(Translation)</span>
                    </div>
                </div>
                @foreach($questionnaires as $index => $question)
                    <?php $question_type = $question['latest_version']['question_type']; ?>
                    <div class="form-group question-form"
                    >
                        <div class="row">
                            <div class="col-md-7">
                                <div class="question">
                                    {{$question['sort_number'].". ".$question['latest_version']['question']}}
                                </div>
                            </div>
                            @if($question_type == "multiple-choice")
                                <div class="col-md-10 multiple-choice-div" answer-div>
                                    @foreach($this->options as $key => $option)
                                        @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                            <div class="option-con">
                                                <input type="radio"
                                                value = "{{ $option['option'] ?? '' }}"
                                                {{ $option['checked'] == 1 ? 'checked' : '' }}
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
                                            <div class="option-con">
                                                <input type="checkbox"
                                                value = "{{ $option['option'] ?? '' }}"
                                                {{ $option['checked'] ? 'checked' : '' }}
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
                                    <?php
                                        $option_key = null; 
                                        $value      = null;
                                        foreach($this->options as $key => $option):
                                            if($option['quiz_questionnaire_version_id'] == $question['latest_version']['id']):
                                                $option_key = $key;
                                                $value = $option['option'];
                                                break;
                                            endif;
                                        endforeach;
                                    ?>
                                    <div class="option-con">
                                        <input type="radio" value="1"
                                        {{ $value !== null && $value == 1 ? 'checked' : '' }}
                                        disabled>
                                        <label for="label-{{$index}}-true">True</label>
                                    </div>
                                    <div class="option-con">
                                        <input type="radio" value="0"
                                        {{ $value !== null && $value == 0 ? 'checked' : '' }}
                                        disabled>
                                        <label for="label-{{$index}}-false">False</label>
                                    </div>
                                </div>
                            @elseif($question_type == "written")
                                <div class="col-md-10 written-div" answer-div>
                                    @foreach ($this->options as $key => $option)
                                        @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                            <div class="option-con">
                                                {{$option['option']}}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if($question_type !== "written")
                                <div class="explanation">
                                    {{$this->questionnaires[$index]['latest_version']['explanation']}}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
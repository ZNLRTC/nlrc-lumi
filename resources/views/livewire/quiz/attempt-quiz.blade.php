<div class="container-fluid" id="quiz-form-body">
    <div class="container">
        @if (session()->has('success'))
            <div class="loading-overlay">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M222.7 32.1c5 16.9-4.6 34.8-21.5 39.8C121.8 95.6 64 169.1 64 256c0 106 86 192 192 192s192-86 192-192c0-86.9-57.8-160.4-137.1-184.1c-16.9-5-26.6-22.9-21.5-39.8s22.9-26.6 39.8-21.5C434.9 42.1 512 140 512 256c0 141.4-114.6 256-256 256S0 397.4 0 256C0 140 77.1 42.1 182.9 10.6c16.9-5 34.8 4.6 39.8 21.5z"/>
                </svg>
                {{ session('success') }}
                <meta http-equiv="refresh" content="1;url={{ url('/quiz/score?attempt=' . Crypt::encrypt($this->attempt_id)) }}">
            </div>
        @endif
        <div class="row">
            <div class="col-md-6 offset-md-3 quiz-header">
                <h2><a href="/{{request()->back ? Crypt::decrypt(request()->back) : 'quiz'}}" title="Go Back" class="t-dblue">
                @include('livewire/quiz/svg/angle-left')
                </a></h2>
                <h2 class="dblue">
                    @include('livewire/quiz/svg/pen')
                    {{$quiz[0]['title']}}
                </h2>
            </div>
            <form class="col-md-6 offset-md-3 attempt-form" id="quiz_form" wire:submit.prevent="submit" {{$this->disabled == true ? "remove-hover" : ''}}>
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
                @if(Auth::user()->role->name == "Trainee")
                    @foreach($questionnaires as $index => $question)
                        <?php $question_type = $question['latest_version']['question_type']; ?>
                        <div class="form-group question-form
                        {{(isset($this->error[$index]) && $this->error[$index] == 'question-'.$index)?'border-red': ''}}"
                        id="question-{{$index}}"
                        >
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="question">
                                        {{$question['sort_number'].". ".$question['latest_version']['question']}}
                                    </div>
                                </div>
                                @if(isset($this->error[$index]) && $this->error[$index] == 'question-'.$index)
                                    <div class="col-md-1">
                                        <i class="t-red">Required</i>
                                    </div>
                                @endif
                                @if($question_type == "multiple-choice")
                                    <div class="col-md-10 multiple-choice-div" answer-div>
                                        @foreach($this->options as $key => $option)
                                            @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                                <div class="option-con">
                                                    <input type="radio"
                                                        wire:model  = "answers.{{$option['quiz_questionnaire_version_id']}}.{{$this->options[$key]['id']}}"
                                                        wire:change = "update_answer({{$option['quiz_questionnaire_version_id']}},{{$this->options[$key]['id']}},'radio')"
                                                        name        = "quiz-{{$index}}-answer"
                                                        id          = "quiz-{{$option['id']}}-option"
                                                        value       = "{{ $option['option'] ?? '' }}"
                                                        {{$this->disabled == true ? 'disabled' : ''}}
                                                    >
                                                    <label class="option" for="quiz-{{$option['id']}}-option">
                                                        {{$this->options[$key]['option']}}
                                                    </label>
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
                                                        wire:model  = "answers.{{$option['quiz_questionnaire_version_id']}}.{{$this->options[$key]['id']}}"
                                                        wire:change = "update_answer({{$option['quiz_questionnaire_version_id']}},{{$this->options[$key]['id']}})"
                                                        id          = "quiz-{{$option['id']}}-option"
                                                        value       = "{{ $option['option'] ?? '' }}"
                                                        {{$this->disabled == true ? 'disabled' : ''}}
                                                    >
                                                    <label class="option" for="quiz-{{$option['id']}}-option">
                                                        {{$this->options[$key]['option']}}
                                                    </label>
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
                                            wire:model = "answers.{{$option['quiz_questionnaire_version_id']}}.{{$this->options[$key]['id']}}"
                                            name       = "quiz-{{$index}}-answer"
                                            id         = "quiz-{{$index}}-true"
                                            {{$this->disabled == true ? 'disabled' : ''}}
                                            >
                                            <label class="option" for="quiz-{{$index}}-true">True</label>
                                        </div>
                                        <div class="option-con">
                                            <input type="radio" value="0"
                                            wire:model = "answers.{{$option['quiz_questionnaire_version_id']}}.{{$this->options[$key]['id']}}"
                                            name       = "quiz-{{$index}}-answer"
                                            id         = "quiz-{{$index}}-false"
                                            {{$this->disabled == true ? 'disabled' : ''}}
                                            >
                                            <label class="option" for="quiz-{{$index}}-false">False</label>
                                        </div>
                                    </div>
                                @elseif($question_type == "written")
                                    <div class="col-md-10 written-div" answer-div>
                                        <textarea rows=1 placeholder="Answer here"
                                        wire:model = "answers.{{$question['latest_version']['id']}}.value"
                                        {{$this->disabled == true ? 'disabled' : ''}}
                                        ></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>                     
                    @endif
                    <button type="submit" class="btn btn-dblue w-full">Submit</button>
                @else
                    <div class="col-md-6 offset-md-3 text-center t-red">
                        <h3><i>Only Students can view and answer this form...</i></h3>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
<div class="container-fluid" id="quiz-form-body">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 quiz-header">
                <h2><a href="/{{request()->back ? Crypt::decrypt(request()->back) : 'quiz'}}" title="Go Back" class="t-dblue">
                @include('livewire/quiz/svg/angle-left')
                </a></h2>
                <h2>UPDATE FORM</h2>
            </div>

            <div class="col-md-6 offset-md-3">
                <!-- Make sure this will update -->
                <form class="form" id="update_quiz_form" wire:submit.prevent="submit">
                    @csrf @method('PUT')
                    <div class="form-group title-div">
                        <div>
                            <textarea name="title" placeholder="TITLE" rows="1"
                            wire:change="update_quiz_info('title',event.target.value)"
                            required>{{$quiz[0]['title']}}</textarea>
                            <div class="count"><span class="char-count">0</span>/1000</div>
                        </div>
                        <div>
                            <textarea name="title-translation" placeholder="TITLE TRANSLATION" rows="1"
                            wire:change="update_quiz_info('title-translation',event.target.value)"
                            required>{{$quiz[0]['title-translation']}}</textarea>
                        </div>
                        <div>
                            <textarea name="description" placeholder="description" rows=1
                            wire:change="update_quiz_info('description',event.target.value)"
                            required>{{$quiz[0]['description']}}</textarea>
                            <div class="count"><span class="char-count">0</span>/1000</div>
                        </div>
                        <div>
                            <textarea name="description-translation" placeholder="description translation"
                            wire:change="update_quiz_info('description-translation',event.target.value)"
                            rows=1 required>{{$quiz[0]['description-translation']}}</textarea>
                        </div>
                        <i class="note mt-4">
                            @include('livewire/quiz/svg/info')
                            Note that updating questions, options or answers will trigger a new quiz version
                        </i>
                    </div>
                    @foreach($questionnaires as $index => $question)
                        <?php $question_type = $question['latest_version']['question_type']; ?>
                        <div class="form-group question-form 
                        {{(isset($this->error[$index]) && $this->error[$index] == 'question-'.$index)?'border-red': ''}}"
                        >
                            <div class="row">
                                <div class="col-md-7">
                                    <textarea placeholder="Question" rows=1 class="question"
                                    name="question-{{$index+1}}"
                                    wire:key="question-{{$question['latest_version']['id']}}"
                                    wire:change="update_content({{$index}}, event.target.value, 'question')"
                                    required>{{$question['latest_version']['question']}}</textarea>
                                </div>
                                <div class="col-md-5">
                                    <select class="form-select" title="Select question type"
                                    wire:change="update_content({{$index}}, event.target.value, 'question_type', '{{$question['latest_version']['id']}}')"
                                    required>
                                        <option value="" {{ $question_type == "" ? "selected" : "" }}>Select question type</option>
                                        <option value="multiple-choice" {{ $question_type == "multiple-choice" ? "selected" : "" }}>Multiple choice</option>
                                        <option value="check-box" {{ $question_type == "check-box" ? "selected" : "" }}>Check box</option>
                                        <option value="boolean" {{ $question_type == "boolean" ? "selected" : "" }}>True or false</option>
                                        <option value="written" {{ $question_type == "written" ? "selected" : "" }}>Written</option>
                                    </select>
                                </div>
                                @if($question_type == "multiple-choice")
                                    <div class="col-md-10 multiple-choice-div" answer-div>
                                        <i class="note">
                                            @include('livewire/quiz/svg/info')
                                            Do not forget to tick an answer below
                                        </i>
                                        @foreach($this->options as $key => $option)
                                            @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                                @include('livewire.quiz.partials.option-con')
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($question_type == "check-box")
                                    <div class="col-md-10 checkbox-div" answer-div>
                                        <i class="note">
                                            @include('livewire/quiz/svg/info')
                                            Do not forget to tick an answer below
                                        </i>
                                        @foreach($this->options as $key => $option)
                                            @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                                @include('livewire.quiz.partials.check-con')
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($question_type == "boolean")
                                    <div class="col-md-10 bool" answer-div>
                                        <i class="note">
                                            @include('livewire/quiz/svg/info')
                                            Do not forget to tick an answer below
                                        </i>
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
                                            id="label-{{$index}}-true"
                                            name="options.{{$index}}.option"
                                            wire:click="update_boolean({{$option_key}}, event.target.value)"
                                            {{ $value !== null && $value == 1 ? 'checked' : '' }}
                                            >
                                            <label for="label-{{$index}}-true">True</label>
                                        </div>
                                        <div class="option-con">
                                            <input type="radio" value="0"
                                            id="label-{{$index}}-false"
                                            name="options.{{$index}}.option"
                                            wire:click="update_boolean({{$option_key}}, event.target.value)"
                                            {{ $value !== null && $value == 0 ? 'checked' : '' }}
                                            >
                                            <label for="label-{{$index}}-false">False</label>
                                        </div>
                                    </div>
                                @elseif($question_type == "written")
                                    <div class="col-md-10 written-div" answer-div>
                                        @foreach ($this->options as $key => $option)
                                            @if ($option['quiz_questionnaire_version_id'] == $question['latest_version']['id'])
                                                <div class="option-con">
                                                    <textarea class="regex" rows=1 placeholder="Add word/phrase"
                                                    wire:change="update_content({{$key}},event.target.value,null)"
                                                    required>{{$option['option']}}</textarea>
                                                    <div class="option-actions">
                                                        @if(count($this->filter_array($question['latest_version']['id'])) > 1)
                                                            @include('livewire/quiz/partials/remove-option-button')
                                                        @endif
                                                        @include('livewire/quiz/partials/add-option-button')
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                @if(isset($this->error[$index]) && $this->error[$index] == 'question-'.$index)
                                    <i class="error-message">Please select an answer</i>
                                @endif
                                    
                                <textarea class="explanation" rows=1 placeholder="Add an explanation (optional)"
                                name="explanation-{{$index+1}}"
                                wire:key="explanation-{{$question['latest_version']['id']}}"
                                wire:change="update_content({{$index}}, event.target.value, 'explanation')">{{$this->questionnaires[$index]['latest_version']['explanation']}}</textarea>

                                <div class="col-md-12 form-action">
                                    @if(count($this->questionnaires) > 1)
                                        <div class="remove-one">
                                            <button type="button" title="Delete question"
                                            wire:click.prevent="remove_question({{$index}})"
                                            >@include('livewire/quiz/svg/trash')</button>
                                            <span> | </span>
                                        </div>
                                    @endif
                                    <button type="button" title="Add question" wire:click.prevent="add_question({{$index+1}},{{count($this->questionnaires)}})">
                                        @include('livewire/quiz/svg/add-circle')
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            Update successful! Redirecting...
                        </div>
                        <meta http-equiv="refresh" content="1;url={{ url('/quiz') }}">
                    @elseif(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>                     
                    @endif

                    @if (!session()->has('success'))
                        <button type="submit" class="btn btn-dblue w-full">Save</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
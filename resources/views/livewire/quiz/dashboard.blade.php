<div class="container">   
    <div class="row card-row">
        <div class="col-md-3 c-body">
            <div class="card">
                <span>
                    Total Attempts
                    @include('livewire/quiz/svg/info')
                </span>
                <h2>{{$total_attempts}}</h2>
                <span>as of {{date("M d, Y")}}</span>
            </div>
        </div>
        <div class="col-md-3 c-body">
            <div class="card">
                <span>
                    Todays' Attempts
                    @include('livewire/quiz/svg/info')
                </span>
                <h2>{{$attempts_today}}</h2>
                <span>{{date("M d, Y")}}</span>
            </div>
        </div>
        <div class="col-md-3 c-body">
            <div class="card">
                <span>
                    Total Paticipants
                    @include('livewire/quiz/svg/info')
                </span>
                <h2>{{$total_participants}}</h2>
                <span>out of {{$total_trainees}} trainees</span>
            </div>
        </div>
        <div class="col-md-3 c-body">
            <div class="card">
                <span>
                    Passing Rate
                    @include('livewire/quiz/svg/info')
                </span>
                <h2>{{round($rate,2)}}%</h2>
                <span>
                    @if($attempts) 
                        -{{round(100-$rate,2)}}% fail
                    @else
                        No attempts made
                    @endif
                </span>
            </div>
        </div>
    </div>
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
                    <th scope="col" class="created-by">CREATED BY</th>
                    <th scope="col">ATTEMPTS</th>
                    <th scope="col" class="action-head">
                        <a href="/quiz/add_quiz"  title="Add Quiz">@include('livewire/quiz/svg/add')</a>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @if(!empty($this->quiz) || $this->quiz !== null)
                    @foreach($this->quiz as $index => $q)
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
                            <td class="creator-column created-by">
                                {{$latest['creator']['name']}}
                                <span>
                                    @include('livewire/quiz/svg/mail')
                                    <i>{{$latest['creator']['email']}}</i>
                                </span>
                            </td>
                            <td class="response-column">
                                {{count($latest['attempts'])}}
                                <?php $total_attempts = $this->total_attempts($q['id']);  ?>
                                <span><i>Total: {{$total_attempts}}</i></span>
                            </td>
                            <td class="action-td">
                                <div>
                                    <div class="button-group">
                                        <a class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="Update" href="/quiz/update?quiz={{Crypt::encrypt($q['version_id'])}}" >@include('livewire/quiz/svg/edit')&nbsp;&nbsp; Update</a>
                                        @if($total_attempts !== 0)
                                            <a class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="View All Attempts" href="/quiz/view_all_attempts?quiz={{Crypt::encrypt($q['id'])}}">@include('livewire/quiz/svg/eye')&nbsp;&nbsp;Attempts</a>
                                        @endif
                                        <button class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700 copy-link" title="Copy Link" wire:click="copyLink('{{url('/')}}/quiz/attempt?quiz={{Crypt::encrypt($q['id'])}}')" wire:ignore>@include('livewire/quiz/svg/link')</button>
                                    </div>
                                    <span
                                    class="{{!$q['archive'] ? 'active' : 'inactive'}}"
                                    title="Click here to update status"
                                    wire:click="archive({{$q['id']}})">
                                        Status: {{!$q['archive'] ? 'Active' : 'Inactive'}}
                                    </span>
                                </div>
                                @if($this->count_version($latest['quiz_id']) > 1)
                                    <button class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" title="View Attempt lists"
                                    wire:click.prevent="toggle_versions({{$latest->quiz_id}})"
                                    >
                                        @include('livewire.quiz.svg.angle-' . (($q["expanded"] == false) ? 'down' : 'up'))
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @foreach($this->quiz_versions as $quiz_version)
                            @if(
                                $quiz_version['id'] !== $latest->id &&
                                $quiz_version['quiz_id'] == $latest->quiz_id
                            )
                                <tr class="{{ $quiz_version['hidden'] ? 'hidden' : '' }} sub-tr">
                                    <td class="title-column">
                                        <div class="d-flex">
                                            @include('livewire/quiz/svg/right-long')
                                            <div>
                                                {{$quiz_version['title']." v.".$quiz_version['version_number']}}
                                                <span title="translation">
                                                    @include('livewire/quiz/svg/refresh')
                                                    <i>{{$quiz_version['title-translation']}}</i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="description-column description">
                                        {{$quiz_version['description']}}
                                        <span title="translation">
                                            @include('livewire/quiz/svg/refresh')
                                            <i>{{$quiz_version['description-translation']}}</i>
                                        </span>
                                    </td>
                                    <td class="creator-column created-by">
                                        {{$quiz_version['creator']['name']}}
                                        <span>
                                            @include('livewire/quiz/svg/mail')
                                            <i>{{$quiz_version['creator']['email']}}</i>
                                        </span>
                                    </td>
                                    <td class="response-column">
                                        {{count($quiz_version['attempts'])}}
                                    </td>
                                    <td class="action-td">
                                        <a title="View questions" class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" href="/quiz/view?quiz={{Crypt::encrypt($quiz_version['id'])}}">View Quiz</a>
                                        @if(count($quiz_version['attempts']) > 0 )
                                            <a title="View Attempts" class="bg-nlrc-blue-500 dark:bg-nlrc-blue-700" href="/quiz/view_all_attempts?qv={{Crypt::encrypt($quiz_version['id'])}}">View Attempts</a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center text-gray"><i>no quiz available</i></td>
                    <tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
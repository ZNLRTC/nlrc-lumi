<x-app-layout>
@section('custom_stylesheet')
    @vite(['resources/css/app.scss','public/css/quiz/main.scss','public/css/quiz/quiz.scss'])
@endsection
    <div class="container-fluid" id="quiz-form-body">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 quiz-header">
                    <h2>
                        <a href="/{{request()->back ? Crypt::decrypt(request()->back) : 'quiz'}}" title="Go Back" class="t-dblue">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                <path d="M41.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 256 246.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                            </svg>
                        </a>
                </h2>
                    <!--  -->
                    <h2>Add Quiz Form</h2>
                </div>
            </div>

            <div class="col-md-6 offset-md-3">
                <form class="form" id="add_quiz_form">
                    @csrf @method('PUT')
                    <div class="form-group title-div">
                        <div>
                            <textarea name="title" placeholder="TITLE" rows="1" required></textarea>
                            <!-- <div class="error-message">required</div> -->
                            <div class="count"><span class="char-count">0</span>/1000</div>
                        </div>
                        <div>
                            <textarea name="title-translation" placeholder="TITLE TRANSLATION" rows="1" required></textarea>
                        </div>
                        <div>
                            <textarea name="description" placeholder="description" rows=1 required></textarea>
                            <div class="count"><span class="char-count">0</span>/1000</div>
                        </div>
                        <div>
                            <textarea name="description-translation" placeholder="description translation" rows=1 required></textarea>
                        </div>
                    </div>

                    <div class="form-group question-form">
                        <div class="row">
                            <div class="col-md-7">
                                <textarea placeholder="Question" rows=1 class="question" required></textarea>
                            </div>
                            <div class="col-md-5">
                                <select class="form-select question-type" title="Select question type" required>
                                    <option value="">Select question type</option>
                                    <option value="multiple-choice">Multiple choice</option>
                                    <option value="check-box">Check box</option>
                                    <option value="boolean">True or false</option>
                                    <option value="written">Written</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 form-action">
                                <button type="button" class="add-question" title="Add question">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dblue w-full">Save</button>
                </form>
            </div>
        </div>
    </div>
@section('custom_script')
    @vite(['public/js/quiz/quiz.js','public/js/quiz/text_area_count.js','public/js/quiz/add_quiz.js'])
@endsection
</x-app-layout>
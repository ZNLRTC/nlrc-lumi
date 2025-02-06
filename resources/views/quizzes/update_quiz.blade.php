<x-app-layout>
@section('custom_stylesheet')
    @vite(['resources/css/app.scss','public/css/quiz/main.scss','public/css/quiz/quiz.scss'])
@endsection
    <div class="w-full" id="quiz-body">
        <livewire:quiz.quiz-profile />
    </div>

@section('custom_script')
    @vite(['public/js/quiz/text_area_count.js'])
@endsection
</x-app-layout>
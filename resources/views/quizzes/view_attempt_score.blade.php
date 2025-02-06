<x-app-layout>
@section('custom_stylesheet')
    @vite(['resources/css/app.scss','public/css/quiz/main.scss','public/css/quiz/quiz.scss', 'public/css/quiz/view_quiz.scss'])
@endsection
    <div class="w-full" id="quiz-body">
        <livewire:quiz.view-attempt-score />
    </div>
</x-app-layout>
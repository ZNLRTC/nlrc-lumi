<x-app-layout>
@section('custom_stylesheet')
    @vite(['resources/css/app.scss','public/css/quiz/main.scss','public/css/quiz/dashboard.scss'])
@endsection
    <div class="w-full" id="quiz-body">
        <livewire:quiz.view-all-attempts />
    </div>
</x-app-layout>
<x-app-layout>
@section('custom_stylesheet')
    @vite(['resources/css/app.scss','public/css/quiz/main.scss','public/css/quiz/dashboard.scss'])
@endsection
    <div class="w-full" id="quiz-body">
        <livewire:quiz.dashboard />
    </div>
    @section('custom_script')
        @vite(['public/js/quiz/copy_link.js'])
    @endsection
</x-app-layout>
<?php

use App\Events\WebsocketTests\Ping;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\DocumentUploadController;
use App\Livewire\NotificationList;

Route::get('/', function () { return view('welcome'); });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications', NotificationList::class)->name('notifications');

    Route::group(['prefix' => 'progress', 'as' => 'progress.'], function() {
        Route::get('/', [ProgressController::class, 'index'])->name('index');
    });

    // Trainees' document routes

    Route::get('/upload', [DocumentUploadController::class, 'showDocumentUpload'])
        ->name('showDocumentUpload')
        ->middleware('allowAccess:Trainee');

    // Routes for courses and units

    Route::get('/courses/kmh', [CourseController::class, 'showKMH'])
        ->name('courses.kmh');

    Route::get('/courses/{course}', [CourseController::class, 'courseIndex'])
        ->name('courses.index')
        ->middleware('checkTraineeCourseAccess');

    Route::get('/courses/{course}/{unit}', [CourseController::class, 'unitIndex'])
        ->name('units.index')
        ->middleware('checkTraineeCourseAccess');

    // Assignment submission route

    Route::get('/courses/{course}/{unit}/{assignment}/submit', [CourseController::class, 'createAssignment'])
        ->name('assignments.create')
        ->middleware(['allowAccess:Trainee', 'checkTraineeCourseAccess']);

    // Meeting routes

    Route::get('/meetings', [MeetingController::class, 'index'])
        ->name('meetings.index')
        ->middleware('allowAccess:Trainee');

    Route::get('/meetings/create', [MeetingController::class, 'create'])
        ->name('meetings.create')
        ->middleware('allowAccess:Instructor,Admin');

    // Knowledge base routes

    Route::get('/help', [KnowledgeBaseController::class, 'index'])
        ->name('kb.index');

    Route::get('/help/{category:slug}/{article:slug}', [KnowledgeBaseController::class, 'show'])
        ->name('kb.show');

    // Audio for courses
    
    Route::get('/audio/{filename}', [AudioController::class, 'getAudio'])
        ->name('audio.get');

    // Announcement routes
    Route::group(['prefix' => 'announcements', 'as' => 'announcements.'], function() {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/{id}', [AnnouncementController::class, 'show'])->name('detail');
    });

    // Route for quizzes
    Route::get("/quiz", [QuizController::class, 'index']);
    Route::any("/quiz/{function}", [QuizController::class, 'callFunction']);

    //Route::get('/quiz-profile/{id}', QuizProfile::class);

    // Exam routes

    Route::group(['prefix' => 'exams', 'as' => 'exams.'], function() {
        Route::get('/', [ExamController::class, 'index'])
            ->name('index')
            ->middleware('allowAccess:Instructor,Editing instructor');
        Route::get('/{type}/{exam}', [ExamController::class, 'show'])
            ->name('show')
            ->middleware('instructorCanGrade');
    });

    Route::get('/results', [ExamController::class, 'results'])
        ->name('exams.results')
        ->middleware('allowAccess:Trainee');

});
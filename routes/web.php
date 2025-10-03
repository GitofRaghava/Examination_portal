<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\CSVImportController;
use App\Http\Controllers\StudentController;
use App\Models\Student;

Route::get('/', function () {
    return view('login');
});
Route::get('/login', function () {
    return view('login');
})->name('login');

// Login/Logout
Route::post('/login', [UserController::class, 'login'])->name('login.post');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Dashboard and Exams routes (web authentication via session)
Route::middleware('web')->group(function () {
    Route::get('/dashboard', [ExamController::class, 'index'])->name('dashboard');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
    Route::get('/exams/{uuid}', [ExamController::class, 'show'])->name('exams.show');
    Route::delete('/exams/{uuid}', [ExamController::class, 'destroy'])->name('exams.destroy'); 
    Route::get('/exams/{uuid}/edit', [ExamController::class, 'edit'])->name('exams.edit');
Route::put('/exams/{uuid}', [ExamController::class, 'update'])->name('exams.update');

    Route::get('/verify-otp', [StudentController::class, 'showVerifyForm'])->name('verify.form');
    Route::post('/verify-otp', [StudentController::class, 'verifyOtp'])->name('verify-otp');
    // NEW STEP 4 ROUTES - Exam Management
    Route::post('/exams/{uuid}/regenerate', [ExamController::class, 'regenerate'])->name('exams.regenerate');
    Route::get('/exams/{uuid}/questions', [ExamController::class, 'getQuestions'])->name('exams.questions');
    Route::post('/exams/{uuid}/attach-question', [ExamController::class, 'attachQuestion'])->name('exams.attach');
    Route::delete('/exams/{uuid}/detach-question/{questionId}', [ExamController::class, 'detachQuestion'])->name('exams.detach');
    Route::get('/exams/{uuid}/preview', [ExamController::class, 'preview'])->name('exams.preview');
    Route::get('/exams/{uuid}/export', [ExamController::class, 'export'])->name('exams.export');
    
    // Step 5 - Admin API routes
    Route::get('/api/questions', [ExamController::class, 'getAllQuestions'])->name('api.questions'); // for QuestionSelector
    
    // CSV Import routes
    Route::get('/csv-import', [CSVImportController::class, 'showImportForm'])->name('csv.import.form');
    Route::post('/csv-import', [CSVImportController::class, 'import'])->name('csv.import');
});

// STEP 6 - Student Exam Taking Routes (Public - No Admin Authentication Required)
Route::prefix('exam')->middleware('web')->group(function () {
    Route::post('/{uuid}/register', [StudentController::class, 'register']) ->name('student.exam.register');
    Route::get('/{uuid}', [StudentController::class, 'examAccess'])->name('student.exam.access');
    Route::get('/{uuid}/take', [StudentController::class, 'takeExam'])->name('student.exam.take');
    Route::post('/{uuid}/save-answer', [StudentController::class, 'saveAnswer'])->name('exam.saveAnswer');
Route::post('/{uuid}/submit', [StudentController::class, 'submitExam'])->name('exam.submit');
Route::get('/{uuid}/submitted', [StudentController::class, 'examSubmitted'])->name('student.exam-submitted');
Route::post('/{uuid}/upload-proctor-videos', [StudentController::class, 'uploadProctorVideos'])
    ->name('upload.proctor.videos');

});

    

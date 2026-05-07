<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::resource('students', Admin\StudentController::class);
    Route::post('students/{student}/reset-password', [Admin\StudentController::class, 'resetPassword'])->name('students.reset-password');
    Route::post('students/bulk-action', [Admin\StudentController::class, 'bulkAction'])->name('students.bulk-action');

    // Batches
    Route::resource('batches', Admin\BatchController::class);

    // Bank Soal
    Route::resource('questions', Admin\QuestionController::class);
    Route::post('questions/bulk-action', [Admin\QuestionController::class, 'bulkAction'])->name('questions.bulk-action');
    Route::post('questions/import', [Admin\QuestionController::class, 'import'])->name('questions.import');
    Route::get('questions/import/template', [Admin\QuestionController::class, 'downloadTemplate'])->name('questions.import.template');

    // Try Out
    Route::resource('tryouts', Admin\TryoutController::class);
    Route::post('tryouts/bulk-action', [Admin\TryoutController::class, 'bulkAction'])->name('tryouts.bulk-action');
    Route::get('tryouts/{tryout}/questions/create', [Admin\TryoutController::class, 'createQuestion'])->name('tryouts.questions.create');
    Route::post('tryouts/{tryout}/questions', [Admin\TryoutController::class, 'storeQuestion'])->name('tryouts.questions.store');
    Route::get('tryouts/{tryout}/questions/bank-select', [Admin\TryoutController::class, 'bankSelectPage'])->name('tryouts.questions.bank-select');
    Route::post('tryouts/{tryout}/questions/bank', [Admin\TryoutController::class, 'addFromBank'])->name('tryouts.questions.bank');
    Route::get('tryouts/{tryout}/questions/import-page', [Admin\TryoutController::class, 'importPage'])->name('tryouts.questions.import-page');
    Route::post('tryouts/{tryout}/questions/import', [Admin\TryoutController::class, 'importQuestions'])->name('tryouts.questions.import');
    Route::delete('tryouts/{tryout}/questions/{question}', [Admin\TryoutController::class, 'removeQuestion'])->name('tryouts.questions.remove');

    // Results
    Route::get('results', [Admin\ResultController::class, 'index'])->name('results.index');
    Route::get('results/{studentTryout}', [Admin\ResultController::class, 'show'])->name('results.show');
    Route::get('results/{studentTryout}/export', [Admin\ResultController::class, 'export'])->name('results.export');
});

// Student routes
Route::prefix('student')->name('student.')->middleware(['auth', 'student'])->group(function () {
    Route::get('/dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');

    // Try Out
    Route::get('tryouts', [Student\TryoutController::class, 'index'])->name('tryouts.index');
    Route::get('tryouts/{tryout}', [Student\TryoutController::class, 'show'])->name('tryouts.show');
    Route::post('tryouts/{tryout}/start', [Student\TryoutController::class, 'start'])->name('tryouts.start');
    Route::get('tryouts/{tryout}/exam/{studentTryout}', [Student\TryoutController::class, 'exam'])->name('tryouts.exam');
    Route::post('tryouts/{tryout}/exam/{studentTryout}/answer', [Student\TryoutController::class, 'saveAnswer'])->name('tryouts.answer');
    Route::post('tryouts/{tryout}/exam/{studentTryout}/flag', [Student\TryoutController::class, 'toggleFlag'])->name('tryouts.flag');
    Route::post('tryouts/{tryout}/exam/{studentTryout}/submit', [Student\TryoutController::class, 'submit'])->name('tryouts.submit');

    // Results
    Route::get('results/{studentTryout}', [Student\ResultController::class, 'show'])->name('results.show');
    Route::get('results/{studentTryout}/discussion', [Student\ResultController::class, 'discussion'])->name('results.discussion');

    // History
    Route::get('history', [Student\HistoryController::class, 'index'])->name('history.index');
});

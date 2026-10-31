<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExaminerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('verification.resend');

Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify-email');

// Exams listing
Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');

// Protected routes - authenticated users
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Exams registration
    Route::post('/exams/{exam}/register', [ExamController::class, 'register'])->name('exams.register');
    Route::delete('/registrations/{registration}/cancel', [ExamController::class, 'cancelRegistration'])->name('registrations.cancel');
    // Students cannot mark payments as paid; admins handle payments via the admin panel.

    // Result details for students
    Route::get('/results/{result}', [DashboardController::class, 'showResult'])->name('results.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin routes
Route::middleware('auth', 'admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/students/{user}', [AdminController::class, 'studentDetails'])->name('student-details');
    
    Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
    Route::get('/exams/create', [AdminController::class, 'createExam'])->name('exams.create');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('exams.store');
    Route::get('/exams/{exam}/edit', [AdminController::class, 'editExam'])->name('exams.edit');
    Route::put('/exams/{exam}', [AdminController::class, 'updateExam'])->name('exams.update');
    Route::delete('/exams/{exam}', [AdminController::class, 'deleteExam'])->name('exams.delete');
    
    Route::get('/exams/completed/results', [AdminController::class, 'completedExamResults'])->name('exams.completed-results');
    Route::get('/exams/{exam}/registrations/export/pdf', [AdminController::class, 'exportRegistrationsPdf'])->name('exams.registrations.export-pdf');
    Route::get('/exams/{exam}/registrations/export/excel', [AdminController::class, 'exportRegistrationsExcel'])->name('exams.registrations.export-excel');
    Route::get('/exams/{exam}/registrations', [AdminController::class, 'examRegistrations'])->name('exams.registrations');
    Route::get('/exams/{exam}/examiners', [AdminController::class, 'manageExaminerAssignments'])->name('exams.examiners');
    Route::post('/exams/{exam}/examiners', [AdminController::class, 'storeExaminerAssignments'])->name('exams.examiners.store');
    Route::post('/registrations/{registration}/confirm', [AdminController::class, 'confirmRegistration'])->name('registrations.confirm');
    Route::post('/registrations/{registration}/restore-confirmed-unpaid', [AdminController::class, 'restoreConfirmedUnpaidRegistration'])->name('registrations.restore-confirmed-unpaid');
    Route::post('/registrations/{registration}/restore-paid', [AdminController::class, 'restorePaidRegistration'])->name('registrations.restore-paid');
    Route::post('/registrations/{registration}/undo-payment-confirmation', [AdminController::class, 'undoPaymentConfirmation'])->name('registrations.undo-payment-confirmation');
    Route::post('/registrations/{registration}/reject', [AdminController::class, 'rejectRegistration'])->name('registrations.reject');
    Route::delete('/registrations/{registration}', [AdminController::class, 'deleteRegistration'])->name('registrations.delete');
    
    Route::get('/exams/{exam}/results', [AdminController::class, 'enterResults'])->name('exams.results');
    Route::get('/exams/{exam}/results/template', [AdminController::class, 'exportResultsTemplate'])->name('exams.results.template');
    Route::post('/exams/{exam}/results/import', [AdminController::class, 'importResults'])->name('exams.results.import');
    Route::get('/exams/{exam}/results-view', [AdminController::class, 'examResults'])->name('exams.results-view');
    Route::get('/exams/{exam}/results/export/pdf', [AdminController::class, 'exportResultsPdf'])->name('exams.results.export-pdf');
    Route::get('/exams/{exam}/results/export/excel', [AdminController::class, 'exportResultsExcel'])->name('exams.results.export-excel');
    Route::post('/registrations/{registration}/result', [AdminController::class, 'storeResult'])->name('results.store');
    Route::post('/exams/{exam}/publish-results', [AdminController::class, 'publishResults'])->name('exams.publish-results');
    Route::get('/examiners/tasks', [AdminController::class, 'examinerTasks'])->name('examiners.tasks');

    // User management - manage roles
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
    Route::get('/users/inactive', [AdminController::class, 'inactiveUsers'])->name('users.inactive');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{user}/manage-users-permission', [AdminController::class, 'updateManageUsersPermission'])->name('users.manage-permission');
    Route::post('/users/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('users.deactivate');
    Route::post('/users/{user}/activate', [AdminController::class, 'activateUser'])->name('users.activate');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
    Route::post('/users/super-admin/reset-password', [AdminController::class, 'resetSuperAdminPassword'])->name('users.reset-super-admin-password');
    Route::get('/payments', [AdminController::class, 'paymentHistory'])->name('payments.history');
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity.logs');
});

// Examiner routes
Route::middleware('auth', 'examiner')->prefix('examiner')->name('examiner.')->group(function () {
    Route::get('/dashboard', [ExaminerController::class, 'dashboard'])->name('dashboard');
    Route::get('/exams/{exam}/results', [ExaminerController::class, 'enterResults'])->name('exams.results');
    Route::post('/registrations/{registration}/result', [ExaminerController::class, 'storeResult'])->name('results.store');
    Route::post('/exams/{exam}/publish-results', [ExaminerController::class, 'publishResults'])->name('exams.publish-results');
});


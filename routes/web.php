<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AccountActivationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\NoCache;

// Authentication Routes
Route::get('/', [LoginController::class, 'showLoginPage']); 
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('login'); 
Route::post('/login', [LoginController::class, 'login'])->name('login.post'); // Changed name to login.post

Route::get('/register', [RegisterController::class, 'showRegisterPage'])->name('register'); 
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::get('/activate-account/{token}', [AccountActivationController::class, 'activate'])->name('activate-account');

// Password Reset Routes
Route::get('password/reset', [PasswordResetController::class, 'showResetRequestForm'])->name('password.request');
Route::post('password/reset', [PasswordResetController::class, 'requestResetPassword'])->name('password.email');
Route::get('password/reset/{token}', [PasswordResetController::class, 'showUpdatePasswordPage'])->name('password.reset');
Route::post('password/reset/{token}', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Protected Routes
Route::middleware(['auth', NoCache::class])->group(function () { // Corrected middleware name
    // Admin Routes
    Route::get('/admin/home', [AdminHomeController::class, 'showHomePage'])->name('admin.home');
    Route::get('/admin/applicant', [ApplicantController::class, 'showApplicantPage'])->name('admin.applicant.view'); // Other admin routes
});

// Logout Route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Welcome Route
Route::get('/welcome', function() {
    return view('welcome');
});

Route::get('/export-applicants', [ApplicantController::class, 'export'])->name('export.applicants');

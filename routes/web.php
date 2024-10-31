<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    AccountActivationController,
    PasswordResetController,
    LogoutController
};
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\RoomController;

use App\Http\Controllers\Student\StudentHomeController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CriteriaController;
use App\Http\Middleware\NoCache;

// Welcome Route
Route::get('/welcome', function () {
    return view('welcome');
});

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
Route::middleware(['auth', NoCache::class])->group(function () {
    // Admin Routes
    Route::get('/admin/home', [AdminHomeController::class, 'showHomePage'])->name('admin.home');
    Route::get('/admin/applicant', [ApplicantController::class, 'showApplicantPage'])->name('admin.applicant.view');
    Route::get('/admin/applicant/invoice', [ApplicantController::class, 'showInvoicePage'])->name('admin.applicant.invoice');

    // Student Routes
    Route::get('/student/home', [StudentHomeController::class, 'showHomePage'])->name('student.home');

    // Additional protected routes can be added here...
});

// Logout Route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Export Routes
Route::get('/export-applicants-excel', [ApplicantController::class, 'downloadExcel'])->name('export.applicants.excel');
Route::get('/export-applicants-pdf', [ApplicantController::class, 'downloadPDF'])->name('export.applicants.pdf'); // Ensure this matches the fetch URL

Route::get('/admin/housing/building',[BuildingController::class,'index'])->name('admin.housing.building');
Route::get('/admin/housing/apartment',[ApartmentController::class,'index'])->name('admin.housing.apartment');
Route::get('/admin/housing/room',[RoomController::class,'index'])->name('admin.housing.room');
Route::get('/admin/reservation/criteria',[CriteriaController::class,'index'])->name('admin.reservation.criteria');
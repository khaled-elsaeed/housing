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
use App\Http\Controllers\StudentPermissionController;

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\RoomController;

use App\Http\Controllers\Student\StudentHomeController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CriteriaController;


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
Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::get('/admin/home', [AdminHomeController::class, 'index'])->name('admin.home');
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
// In routes/web.php

Route::get('/admin/buildings/create', [BuildingsController::class, 'create'])->name('admin.buildings.store');

Route::get('/admin/housing/building',[BuildingController::class,'index'])->name('admin.housing.building');
Route::get('/admin/housing/apartment',[ApartmentController::class,'index'])->name('admin.housing.apartment');
Route::get('/admin/housing/room',[RoomController::class,'index'])->name('admin.housing.room');
Route::get('/admin/reservation/criteria',[CriteriaController::class,'index'])->name('admin.reservation.criteria');

// Routes for students
Route::get('/permissions', [StudentPermissionController::class, 'index'])->name('admin.student-permissions.index');
Route::get('/permissions/{permission}/request', [StudentPermissionController::class, 'createRequest'])->name('admin.permissions.createRequest');
Route::post('/permissions/{permission}/request', [StudentPermissionController::class, 'storeRequest'])->name('admin.permissions.storeRequest');

Route::get('/permissions/manage', [StudentPermissionController::class, 'manage'])->name('admin.student-permissions.manage');
Route::post('/permissions/store', [StudentPermissionController::class, 'store'])->name('admin.student-permissions.store');
Route::post('/permissions/update/{id}', [StudentPermissionController::class, 'update'])->name('admin.student-permissions.update');
Route::delete('/permissions/destroy/{id}', [StudentPermissionController::class, 'destroy'])->name('admin.student-permissions.destroy');
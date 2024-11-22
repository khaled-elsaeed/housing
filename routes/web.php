<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    AccountActivationController,
    PasswordResetController,
    LogoutController
};
use App\Http\Controllers\Admin\{
    AdminHomeController,
};
use App\Http\Controllers\Admin\Applicant\{
    ApplicantController,
    ApplicantDocumentController,
};
use App\Http\Controllers\Student\StudentHomeController;
use App\Http\Controllers\Admin\Unit\{
    BuildingController,
    ApartmentController,
    RoomController
};

// Welcome Route
Route::get('/welcome', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/', [LoginController::class, 'showLoginPage']);
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'showRegisterPage'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::get('/activate-account/{token}', [AccountActivationController::class, 'activate'])->name('activate-account');

// Password Reset Routes
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/reset', [PasswordResetController::class, 'showResetRequestForm'])->name('request');
    Route::post('/reset', [PasswordResetController::class, 'requestResetPassword'])->name('email');
    Route::get('/reset/{token}', [PasswordResetController::class, 'showUpdatePasswordPage'])->name('reset');
    Route::post('/reset/{token}', [PasswordResetController::class, 'resetPassword'])->name('update');
});

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard and Applicant Routes
        Route::get('/home', [AdminHomeController::class, 'index'])->name('home');
        Route::get('/applicant', [ApplicantController::class, 'index'])->name('applicant.view');
        Route::get('/applicant/documents', [ApplicantDocumentController::class, 'index'])->name('applicant.documents.view');
        Route::get('/admin/applicant/documents/{id}', [ApplicantDocumentController::class, 'getDocuments'])->name('applicant.documents.get-documents');

        // Unit Management (Building, Apartment, Room Routes)
        Route::prefix('unit')->name('unit.')->group(function () {
            // Building Routes
            Route::prefix('building')->group(function () {
                Route::get('/', [BuildingController::class, 'index'])->name('building');
                Route::post('/store', [BuildingController::class, 'store'])->name('building.store');
                Route::delete('/delete/{id}', [BuildingController::class, 'destroy'])->name('building.destroy');
                Route::get('/export', [BuildingController::class, 'downloadBuildingsExcel'])->name('building.export-excel');
                Route::post('/update-status', [BuildingController::class, 'updateStatus'])->name('building.update-status');
                Route::post('/update-note', [BuildingController::class, 'updateNote'])->name('building.update-note');
            });

            // Apartment Routes
            Route::prefix('apartment')->group(function () {
                Route::get('/', [ApartmentController::class, 'index'])->name('apartment');
                Route::post('/store', [ApartmentController::class, 'store'])->name('apartment.store');
                Route::delete('/delete/{id}', [ApartmentController::class, 'destroy'])->name('apartment.destroy');
                Route::get('/export', [ApartmentController::class, 'downloadApartmentsExcel'])->name('apartment.export-excel');
                Route::post('/update-status', [ApartmentController::class, 'updateStatus'])->name('apartment.update-status');
                Route::post('/update-note', [ApartmentController::class, 'updateNote'])->name('apartment.update-note');
            });

            Route::prefix('room')->group(function () {
                Route::get('/', [RoomController::class, 'index'])->name('room');
                Route::post('/store', [RoomController::class, 'store'])->name('room.store');
                Route::delete('/delete/{id}', [RoomController::class, 'destroy'])->name('room.destroy');
                Route::get('/export', [RoomController::class, 'downloadRoomsExcel'])->name('room.export-excel');
                Route::post('/update-status', [RoomController::class, 'updateStatus'])->name('room.update-status');
                Route::post('/update-note', [RoomController::class, 'updateNote'])->name('room.update-note');
            });

            // Room Routes
            Route::get('/room', [RoomController::class, 'index'])->name('room');
        });
    });

    // Student Routes
    Route::get('/student/home', [StudentHomeController::class, 'showHomePage'])->name('student.home');
});

// Logout Route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Export Routes
Route::prefix('export')->name('export.')->group(function () {
    Route::get('/applicants/excel', [ApplicantController::class, 'downloadApplicantsExcel'])->name('applicants.excel');
    Route::get('/applicants/pdf', [ApplicantController::class, 'downloadApplicantsPDF'])->name('applicants.pdf');
});

// Applicant Email Routes
Route::get('/get-university-email/{id}', [ApplicantController::class, 'getApplicantAcademicEmail'])->name('get.university.email');
Route::post('/update-applicant-email', [ApplicantController::class, 'updateEmail'])->name('update.applicant.email');
Route::get('/get-applicant-details/{id}', [ApplicantController::class, 'getApplicantMoreDetails'])->name('admin.applicant.more-details');

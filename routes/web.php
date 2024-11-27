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
    AdminProfileController,
    AdminSettingsController,
    AdminMaintenanceController,

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

use App\Http\Controllers\Admin\PermissionRequest\PermissionRequestController;

use App\Http\Controllers\Admin\Resident\ResidentController;
use App\Http\Controllers\Student\StudentMaintenanceController;

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
        Route::get('/applicant/documents', [ApplicantDocumentController::class, 'index'])->name('applicant.document.view');
        Route::get('/applicants/documents/excel', [ApplicantDocumentController::class, 'downloadApplicantsExcel'])->name('applicant.document.excel');

        Route::get('/applicant/documents/{id}', [ApplicantDocumentController::class, 'getDocuments'])->name('applicant.document.get-documents');
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
        // Update profile
        Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/update-picture', [AdminProfileController::class, 'updateProfilePicture'])->name('profile.update-picture');
        Route::delete('profile/delete-picture', [AdminProfileController::class, 'deleteProfilePicture'])->name('profile.delete-picture');
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
                Route::post('/update-details', [RoomController::class, 'updateRoomDetails'])->name('room.update-details');
                Route::post('/update-note', [RoomController::class, 'updateNote'])->name('room.update-note');
            });

            // Room Routes
            Route::get('/room', [RoomController::class, 'index'])->name('room');
        });


Route::prefix('residents')->name('residents.')->group(function () {

    // Route to display the list of residents
    Route::get('/', [ResidentController::class, 'index'])->name('index');

    // Route to download residents' data as an Excel file
    Route::get('/export/excel', [ResidentController::class, 'downloadResidentsExcel'])->name('export-excel');

    // Route to download residents' data as a PDF file
    Route::get('/export/pdf', [ResidentController::class, 'downloadResidentsPDF'])->name('export.pdf');

    // Route to get more details about a specific resident
    Route::get('/details/{id}', [ResidentController::class, 'getResidentMoreDetails'])->name('more-details');
});


         // Index page for listing all permission requests
    Route::get('/permissions', [PermissionRequestController::class, 'index'])->name('permissions.index');

    // Show a specific permission request details
    Route::get('/permissions/{id}', [PermissionRequestController::class, 'show'])->name('permissions.show');

    // Update the status of a permission request (Approve or Reject)
    Route::post('/permissions/{id}/approve', [PermissionRequestController::class, 'approve'])->name('permissions.approve');
    Route::post('/permissions/{id}/reject', [PermissionRequestController::class, 'reject'])->name('permissions.reject');




       Route::get('/maintenance',[AdminMaintenanceController::class,'index'])->name('maintenance.index');
       Route::get('/maintenance/excel',[AdminMaintenanceController::class,'downloadMaintenanceRequestsExcel'])->name('maintenance.excel');
       Route::put('maintenance/update-status/{id}', [AdminMaintenanceController::class, 'updateStatus'])
       ->name('maintenance.updateStatus');
        Route::get('settings', [AdminSettingsController::class, 'index'])->name('setting');
    Route::post('settings/reservation-update', [AdminSettingsController::class, 'updateReservationSettings'])->name('setting.update-reservation');
    });

    // Student Routes
    Route::get('/student/home', [StudentHomeController::class, 'index'])->name('student.home');

Route::prefix('student')->name('student.')->group(function () {
    // Show the maintenance request form
    Route::get('maintenance', [StudentMaintenanceController::class, 'showForm'])->name('maintenance.form');

    // Handle the form submission
    Route::post('maintenance', [StudentMaintenanceController::class, 'store'])->name('maintenance.store');
});

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

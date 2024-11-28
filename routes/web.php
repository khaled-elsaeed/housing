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
};


use App\Http\Controllers\Admin\Reservation\ReservationController;

use App\Http\Controllers\Student\StudentHomeController;
use App\Http\Controllers\Admin\Unit\{
    BuildingController,
    ApartmentController,
    RoomController
};
use App\Http\Controllers\Admin\PermissionRequest\PermissionRequestController;
use App\Http\Controllers\Admin\Resident\ResidentController;
use App\Http\Controllers\Student\StudentMaintenanceController;
use App\Http\Controllers\DataTableController;

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

        // Admin Dashboard
        Route::get('/home', [AdminHomeController::class, 'index'])->name('home');

        // Applicant Routes
        Route::prefix('applicants')->name('applicants.')->group(function () {
            Route::get('/', [ApplicantController::class, 'index'])->name('index'); // View applicant page
            Route::get('/data', [ApplicantController::class, 'fetchApplicants'])->name('fetch'); // Fetch applicants data
            Route::get('/summary', [ApplicantController::class, 'getApplicantsSummary'])->name('get-summary'); // Fetch applicants summary statistics
            Route::get('/{id}', [ApplicantController::class, 'show'])->name('show'); // Fetch details for a specific applicant

            // Export Routes
            Route::prefix('export')->name('export-')->group(function () {
                Route::get('/excel', [ApplicantController::class, 'downloadApplicantsExcel'])->name('excel'); // Export applicants to Excel
            });
        });

        // Admin Profile Routes
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
        Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/update-picture', [AdminProfileController::class, 'updateProfilePicture'])->name('profile.update-picture');
        Route::delete('profile/delete-picture', [AdminProfileController::class, 'deleteProfilePicture'])->name('profile.delete-picture');

        // Unit Management (Building, Apartment, Room Routes)
        Route::prefix('unit')->name('unit.')->group(function () {
            Route::prefix('building')->group(function () {
                Route::get('/', [BuildingController::class, 'index'])->name('building');
                Route::post('/store', [BuildingController::class, 'store'])->name('building.store');
                Route::delete('/delete/{id}', [BuildingController::class, 'destroy'])->name('building.destroy');
                Route::get('/export', [BuildingController::class, 'downloadBuildingsExcel'])->name('building.export-excel');
                Route::post('/update-status', [BuildingController::class, 'updateStatus'])->name('building.update-status');
                Route::post('/update-note', [BuildingController::class, 'updateNote'])->name('building.update-note');
                Route::get('/empty',[BuildingController::class,'fetchEmptyBuildings'])->name('building.fetch-empty');
            });

            Route::prefix('apartment')->group(function () {
                Route::get('/', [ApartmentController::class, 'index'])->name('apartment');
                Route::post('/store', [ApartmentController::class, 'store'])->name('apartment.store');
                Route::delete('/delete/{id}', [ApartmentController::class, 'destroy'])->name('apartment.destroy');
                Route::get('/export', [ApartmentController::class, 'downloadApartmentsExcel'])->name('apartment.export-excel');
                Route::post('/update-status', [ApartmentController::class, 'updateStatus'])->name('apartment.update-status');
                Route::post('/update-note', [ApartmentController::class, 'updateNote'])->name('apartment.update-note');
                Route::get('/empty/{buildingId}', [ApartmentController::class, 'fetchEmptyApartments'])->name('apartment.fetch-empty');

            });

            Route::prefix('room')->group(function () {
                Route::get('/', [RoomController::class, 'index'])->name('room');
                Route::post('/store', [RoomController::class, 'store'])->name('room.store');
                Route::delete('/delete/{id}', [RoomController::class, 'destroy'])->name('room.destroy');
                Route::get('/export', [RoomController::class, 'downloadRoomsExcel'])->name('room.export-excel');
                Route::post('/update-details', [RoomController::class, 'updateRoomDetails'])->name('room.update-details');
                Route::post('/update-note', [RoomController::class, 'updateNote'])->name('room.update-note');
                Route::get('/empty/{apartmentId}', [RoomController::class, 'fetchEmptyRooms'])->name('room.fetch-empty');

            });
        });

        Route::prefix('reservation')->name('reservation.')->group(function () {
            Route::get('/relocation', [ReservationController::class, 'index'])->name('relocation');
            Route::get('/{userId}', [ReservationController::class, 'show'])->name('show');
            Route::post('/swap', [ReservationController::class, 'swapReservationLocation'])->name('swap');
            Route::post('/reallocate', [ReservationController::class, 'reallocateReservation'])->name('reallocate');

        });
        

        // Resident Routes
        Route::prefix('residents')->name('residents.')->group(function () {
            Route::get('/', [ResidentController::class, 'index'])->name('index');
            Route::get('/data', [ResidentController::class, 'fetchResidents'])->name('fetch');
            Route::get('/summary', [ResidentController::class, 'getSummary'])->name('get-summary');
            Route::get('/export/excel', [ResidentController::class, 'downloadResidentsExcel'])->name('export-excel');
            Route::get('/export/pdf', [ResidentController::class, 'downloadResidentsPDF'])->name('export.pdf');
            Route::get('/details/{id}', [ResidentController::class, 'getResidentMoreDetails'])->name('more-details');
        });

        // Permission Request Routes
        Route::get('/permissions', [PermissionRequestController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{id}', [PermissionRequestController::class, 'show'])->name('permissions.show');
        Route::post('/permissions/{id}/approve', [PermissionRequestController::class, 'approve'])->name('permissions.approve');
        Route::post('/permissions/{id}/reject', [PermissionRequestController::class, 'reject'])->name('permissions.reject');

        // Maintenance and Settings
        Route::get('/maintenance', [AdminMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/excel', [AdminMaintenanceController::class, 'downloadMaintenanceRequestsExcel'])->name('maintenance.excel');
        Route::put('maintenance/update-status/{id}', [AdminMaintenanceController::class, 'updateStatus'])->name('maintenance.updateStatus');
        Route::get('settings', [AdminSettingsController::class, 'index'])->name('setting');
        Route::post('settings/reservation-update', [AdminSettingsController::class, 'updateReservationSettings'])->name('setting.update-reservation');
    }); // End Admin Routes

    // Student Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/home', [StudentHomeController::class, 'index'])->name('home');
        Route::get('maintenance', [StudentMaintenanceController::class, 'showForm'])->name('maintenance.form');
        Route::post('maintenance', [StudentMaintenanceController::class, 'store'])->name('maintenance.store');
    });
});

// Logout Route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

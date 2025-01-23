<?php

use Illuminate\Support\Facades\App;

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
use App\Http\Controllers\Admin\Invoice\InvoiceController;

use App\Http\Controllers\Student\StudentMaintenanceController;
use App\Http\Controllers\Student\StudentPermissionController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\LocalizationController;

use App\Http\Controllers\CompleteProfileController;

use App\Http\Controllers\DataTableController;
use App\Http\Controllers\Admin\Account\StudentAccountController;
use App\Http\Middleware\Localization;

Route::get('localization/{local}',LocalizationController::class)->name('localization');

Route::middleware(Localization::class)
->group(function(){
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

        // Admin Routes (require admin role)
        Route::prefix('admin')->name('admin.')->middleware('can:is-admin')->group(function () {

        

            Route::prefix('home')->name('home')->group(function () {
                Route::get('/', [AdminHomeController::class, 'showDashboard'])->name('');
                Route::get('/stats', [AdminHomeController::class, 'fetchStats'])->name('.stats');
            });
            

            Route::prefix('applicants')->name('applicants')->group(function () {
                Route::get('/', [ApplicantController::class, 'showApplicantPage'])->name(''); 
                Route::get('/fetch', [ApplicantController::class, 'fetchApplicants'])->name('.fetch'); 
                Route::get('/stats', [ApplicantController::class, 'fetchStats'])->name('.stats'); 
                Route::get('/{id}', [ApplicantController::class, 'fetchApplicantInfo'])->name('.details');
                // Export Routes
                Route::prefix('export')->name('.export-')->group(function () {
                    Route::get('/excel', [ApplicantController::class, 'downloadApplicantsExcel'])->name('excel'); 
                });
            });

            Route::prefix('invoices')->name('invoices')->group(function () {
                Route::get('/', [InvoiceController::class, 'showInvoicesPage'])->name('.index');  // Show invoices page
                Route::get('/fetch', [InvoiceController::class, 'fetchInvoices'])->name('.fetch');  // Fetch invoices data for DataTables
                Route::get('/stats', [InvoiceController::class, 'fetchStats'])->name('.stats');  // Fetch statistics for invoices
                Route::get('/{id}', [InvoiceController::class, 'fetchInvoice'])->name('.show');  // Fetch a specific invoice by ID
                Route::post('/payment/{paymentId}/status', [InvoiceController::class, 'updatePaymentStatus'])->name('.payment.update'); // Update payment status
                Route::prefix('export')->name('.export-')->group(function () {
                    Route::get('/excel', [InvoiceController::class, 'downloadInvoicesExcel'])->name('excel');  // Export invoices to Excel
                });
            });


            Route::prefix('account')->name('account.')->group(function () {
                // Show the student account page
                Route::get('students', [StudentAccountController::class, 'showStudentPage'])->name('student.index');

                // Edit student email
                Route::post('students/edit-email', [StudentAccountController::class, 'editEmail'])->name('student.editEmail');
            
                // Reset student password
                Route::post('students/reset-password', [StudentAccountController::class, 'resetPassword'])->name('student.resetPassword');
            });

            

            // Admin Profile Routes
            Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
            Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
            Route::post('profile/update-picture', [AdminProfileController::class, 'updateProfilePicture'])->name('profile.update-picture');
            Route::delete('profile/delete-picture', [AdminProfileController::class, 'deleteProfilePicture'])->name('profile.delete-picture');

            // Unit Management (Building, Apartment, Room Routes)
            Route::prefix('unit')->name('unit.')->group(function () {
                Route::prefix('building')->group(function () {
                    Route::get('/', [BuildingController::class, 'showBuildingPage'])->name('building');
                    Route::get('/fetch', [BuildingController::class, 'fetchBuildings'])->name('building.fetch'); 
                    Route::get('/stats', [BuildingController::class, 'fetchStats'])->name('building.stats'); 
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
                // Reservation routes
                Route::get('/', [ReservationController::class, 'index'])->name('index');
                Route::get('/export-excel', [ReservationController::class, 'exportExcel'])->name('export-excel');
                Route::get('/{id}/show', [ReservationController::class, 'show'])->name('show');
                Route::get('/fetch', [ReservationController::class, 'fetchReservations'])->name('fetch');
                Route::get('/get-summary', [ReservationController::class, 'getReservationsSummary'])->name('get-summary');
            
                // Relocation routes
                Route::prefix('relocation')->name('relocation.')->group(function () {
                    Route::get('/', [ReservationController::class, 'relocation'])->name('index');
                    Route::get('/{userId}', [ReservationController::class, 'show'])->name('show');
                    Route::post('/swap', [ReservationController::class, 'swapReservationLocation'])->name('swap');
                    Route::post('/reallocate', [ReservationController::class, 'reallocateReservation'])->name('reallocate');
                });
            });
            


            

            // Resident Routes
            Route::prefix('residents')->name('residents.')->group(function () {
                Route::get('/', [ResidentController::class, 'index'])->name('index');
                Route::get('/create', [ResidentController::class, 'create'])->name('create');

                Route::get('/data', [ResidentController::class, 'fetchResidents'])->name('fetch');
                Route::get('/summary', [ResidentController::class, 'getSummary'])->name('get-summary');
                Route::get('/export/excel', [ResidentController::class, 'downloadResidentsExcel'])->name('export-excel');
                Route::get('/export/pdf', [ResidentController::class, 'downloadResidentsPDF'])->name('export.pdf');
                Route::get('/details/{id}', [ResidentController::class, 'getResidentMoreDetails'])->name('more-details');
                Route::post('/fetch-details', [ResidentController::class, 'getStudentData'])->name('fetch-details');
                Route::post('/store', [ResidentController::class, 'createResident'])->name('store');


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
            Route::get('/maintenance/issues/{id}', [AdminMaintenanceController::class, 'getIssues'])->name('maintenance.getIssues');

            Route::get('settings', [AdminSettingsController::class, 'index'])->name('setting');
            Route::post('settings/reservation-update', [AdminSettingsController::class, 'updateReservationSettings'])->name('setting.update-reservation');
        }); // End Admin Routes

        // Student Routes
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/home', [StudentHomeController::class, 'index'])->name('home');
            Route::get('maintenance', [StudentMaintenanceController::class, 'showForm'])->name('maintenance.form');
            Route::post('maintenance/store', [StudentMaintenanceController::class, 'store'])->name('maintenance.store');

            Route::get('permission', [StudentPermissionController::class, 'showForm'])->name('permission.form');
            Route::post('permission/store', [StudentPermissionController::class, 'store'])->name('permission.store');
        });
    });

    // Logout Route
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('/get-cities/{governorateId}', [StudentProfileController::class, 'getCitiesByGovernorate'])->name('get-cities');
    Route::get('/get-programs/{facultyId}', [StudentProfileController::class, 'getProgramsForFaculty'])->name('get-programs');
    Route::get('/profile', [StudentProfileController::class, 'index'])->name('student.profile');
    Route::put('/profile/update', [StudentProfileController::class, 'update'])->name('student.profile.update');
    Route::post('profile/update-picture', [StudentProfileController::class, 'updateProfilePicture'])->name('student.profile.update-picture');
    Route::DELETE('profile/delete-picture', [StudentProfileController::class, 'deleteProfilePicture'])->name('student.profile.delete-picture');
    Route::put('/student/address', [StudentProfileController::class, 'updateAddress'])->name('student.updateAddress');
    Route::put('/student/academic-info', [StudentProfileController::class, 'updateAcademicInfo'])->name('student.updateAcademicInfo');
    Route::put('/student/update-parent-info', [StudentProfileController::class, 'updateParentInfo'])->name('student.updateParentInfo');
    Route::POST('/student/sibling-info', [StudentProfileController::class, 'updateOrCreateSiblingInfo'])->name('student.updateOrCreateSiblingInfo');
    Route::POST('/student/emergency-info', [StudentProfileController::class, 'updateOrCreateEmergencyInfo'])->name('student.updateOrCreateEmergencyInfo');


    Route::get('complete-profile', [CompleteProfileController::class, 'index'])->name('profile.complete');
    Route::post('complete-profile/store', [CompleteProfileController::class, 'store'])->name('profile.store');

    
});

use App\Http\Controllers\StudentPaymentController;

// Route to handle payment receipt upload
Route::post('/student/payment/upload', [StudentPaymentController::class, 'payInvoice'])->name('student.invoice.pay');
Route::post('/student/invoice/detail', [StudentPaymentController::class, 'getInvoiceDetails'])->name('student.payment.info');
Route::post('/student/invoice/add', [StudentPaymentController::class, 'addInvoice'])->name('student.payment.add');

use App\Http\Controllers\UploadController;

Route::post('upload', UploadController::class)->name('upload');



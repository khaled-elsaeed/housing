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
};

use App\Http\Controllers\App\NotificationController;


use App\Http\Controllers\Staff\StaffMaintenanceController;

use App\Http\Controllers\Admin\Maintenance\{
    MaintenanceController,
};


use App\Http\Controllers\Admin\Reservation\{ReservationController,ReservationRequestsController,ReservationSwapController};
use App\Http\Controllers\App\UploadController;

use App\Http\Controllers\Student\StudentHomeController;
use App\Http\Controllers\Student\StudentReservationRequestController;
use App\Http\Controllers\Student\StudentPaymentController;


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
use App\Http\Controllers\App\LocalizationController;

use App\Http\Controllers\Student\StudentProfileCompleteController; 

use App\Http\Controllers\DataTableController;
use App\Http\Controllers\Admin\Account\{ResidentAccountController,StaffAccountController};
use App\Http\Middleware\Localization;
use App\Http\Controllers\AcademicTermController;


Route::get('localization/{local}',LocalizationController::class)->name('localization');

Route::middleware(Localization::class)
->group(function(){
    // Authentication Routes
    Route::get('/', [LoginController::class, 'showLoginPage']);
    Route::get('/login', [LoginController::class, 'showLoginPage'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegisterPage'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    Route::get('/activate-account/{token}', [AccountActivationController::class, 'activate'])
    ->middleware('throttle:10,1')
    ->name('activate-account');
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


            Route::post('/academic-terms', [AcademicTermController::class, 'create'])->name('academic.create');
            Route::post('/academic-terms/{id}/start', [AcademicTermController::class, 'start'])->name('academic.start');
            Route::post('/academic-terms/{id}/end', [AcademicTermController::class, 'end'])->name('academic.end');

            Route::prefix('home')->name('home')->group(function () {
                Route::get('/', [AdminHomeController::class, 'showDashboard'])->name('');
                Route::get('/stats', [AdminHomeController::class, 'fetchStats'])->name('.stats');
            });
            

            Route::prefix('invoices')->name('invoices')->group(function () {
                Route::get('/', [InvoiceController::class, 'showInvoicesPage'])->name('.index');  // Show invoices page
                Route::get('/fetch', [InvoiceController::class, 'fetchInvoices'])->name('.fetch');  // Fetch invoices data for DataTables
                Route::get('/stats', [InvoiceController::class, 'fetchStats'])->name('.stats');  // Fetch statistics for invoices
                Route::get('/{id}', [InvoiceController::class, 'fetchInvoice'])->name('.show');  // Fetch a specific invoice by ID
                Route::post('/payment/{paymentId}/status', [InvoiceController::class, 'updatePaymentStatus'])->name('.payment.update'); // Update payment status
                Route::post('/{invoiceId}/details', [InvoiceController::class, 'updateInvoiceDetails'])->name('.details.update');
                Route::prefix('export')->name('.export-')->group(function () {
                    Route::get('/excel', [InvoiceController::class, 'downloadInvoicesExcel'])->name('excel');  // Export invoices to Excel
                });
            });


            Route::prefix('account')->name('account.')->group(function () {
                // Show the student account page
                Route::get('users', [ResidentAccountController::class, 'showUserPage'])->name('resident.index');
            
                // Edit student email
                Route::post('users/edit-email', [ResidentAccountController::class, 'editEmail'])->name('resident.editEmail');
            
                // Reset passwords
                Route::post('users/all-users/reset-password', [ResidentAccountController::class, 'resetAllUsersPasswords'])->name('resident.resetAllPasswords');
                Route::post('users/reset-password', [ResidentAccountController::class, 'resetPassword'])->name('resident.resetPassword');
            
                // ========================== STAFF ROUTES ========================== //
                Route::prefix('staff')->name('staff.')->group(function () {
                    // Show the staff account page
                    Route::get('/', [StaffAccountController::class, 'index'])->name('index');
            
                    // Add a new staff member
                    Route::post('add', [StaffAccountController::class, 'store'])->name('store');
            
                    // Edit staff member details
                    Route::post('edit', [StaffAccountController::class, 'update'])->name('update');
            
                    // Delete a staff member
                    Route::post('delete', [StaffAccountController::class, 'destroy'])->name('destroy');
            
                    // Reset staff email
                    Route::post('edit-email', [StaffAccountController::class, 'editEmail'])->name('editEmail');
            
                    // Reset staff password
                    Route::post('reset-password', [StaffAccountController::class, 'resetPassword'])->name('resetPassword');
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
                    Route::get('/', [ReservationSwapController::class, 'index'])->name('index');
                    Route::get('/{userId}', [ReservationSwapController::class, 'show'])->name('show');
                    Route::post('/swap', [ReservationSwapController::class, 'swapReservationLocation'])->name('swap');
                    Route::post('/reallocate', [ReservationSwapController::class, 'reallocateReservation'])->name('reallocate');
                });
            });

            Route::prefix('reservation-requests')->name('reservation-requests.')->group(function () {
                // Main views
                Route::get('/', [ReservationRequestsController::class, 'index'])->name('index');
                
                // Data fetching routes
                Route::get('/fetch', [ReservationRequestsController::class, 'fetch'])->name('fetch');
                Route::get('/get-summary', [ReservationRequestsController::class, 'getSummary'])->name('get-summary');
                
                // Action routes
                Route::post('/auto-reserve', [ReservationRequestsController::class, 'autoReserve'])->name('auto-reserve');
                Route::post('/{id}/accept', [ReservationRequestsController::class, 'accept'])->name('accept');
                Route::post('/{id}/reject', [ReservationRequestsController::class, 'reject'])->name('reject');
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
            Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
            Route::get('/maintenance/fetch', [MaintenanceController::class, 'fetchRequests'])->name('maintenance.requests.fetch');
            Route::get('/maintenance/fetchStaff', [MaintenanceController::class, 'fetchStaff'])->name('maintenance.requests.fetchStaff');
            Route::post('/maintenance/assignStaff/{id}', [MaintenanceController::class, 'assign'])->name('maintenance.requests.assign');


            Route::get('settings', [AdminSettingsController::class, 'index'])->name('setting');
            Route::post('settings/reservation-update', [AdminSettingsController::class, 'updateReservationSettings'])->name('setting.update-reservation');
        }); // End Admin Routes

        // Student Routes
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/home', [StudentHomeController::class, 'index'])->name('home');

            Route::get('/maintenance', [StudentMaintenanceController::class, 'index'])->name('maintenance.index');
            Route::get('/maintenance/create', [StudentMaintenanceController::class, 'create'])->name('maintenance.create');
            Route::post('/maintenance/store', [StudentMaintenanceController::class, 'store'])->name('maintenance.store');
            Route::get('/maintenance/{id}', [StudentMaintenanceController::class, 'show'])->name('maintenance.show');
            Route::get('/student/maintenance/requests', [StudentMaintenanceController::class, 'fetchUserRequests'])->name('maintenance.requests');
            // Additional maintenance actions
            Route::post('/maintenance/{id}/cancel', [StudentMaintenanceController::class, 'cancel'])->name('maintenance.cancel');
            Route::post('/maintenance/{id}/comment', [StudentMaintenanceController::class, 'addComment'])->name('maintenance.comment');
            Route::post('/maintenance/{id}/confirm-resolution', [StudentMaintenanceController::class, 'confirmResolution'])->name('maintenance.confirm-resolution');
            Route::post('/maintenance/{id}/report-unresolved', [StudentMaintenanceController::class, 'reportUnresolved'])->name('maintenance.report-unresolved');
            Route::get('/maintenance/{id}/problems-by-category', [StudentMaintenanceController::class, 'getProblemsByCategory'])->name('maintenance.problems.by.category');

            Route::post('/reservation',[StudentReservationRequestController::class,'store'])->name('reservation.store');
            
            Route::get('/invoices/{invoiceId}/media', [StudentPaymentController::class, 'getInvoiceMedia'])->name('invoices.media');
            Route::post('/invoice/{invoiceId}/update-media', [StudentPaymentController::class, 'updateMedia'])->name('invoice.update-media');
            Route::get('permission', [StudentPermissionController::class, 'showForm'])->name('permission.form');
            Route::post('permission/store', [StudentPermissionController::class, 'store'])->name('permission.store');
        });
        Route::get('/home', [StaffMaintenanceController::class, 'index'])->name('staff.home');

        Route::get('/maintenance', [StaffMaintenanceController::class, 'index'])->name('staff.maintenance.index');
    Route::post('/maintenance/{id}/accept', [StaffMaintenanceController::class, 'accept'])->name('staff.maintenance.accept');
    Route::post('/maintenance/{id}/complete', [StaffMaintenanceController::class, 'complete'])->name('staff.maintenance.complete');

    Route::get('/maintenance/fetch', [StaffMaintenanceController::class, 'fetchRequests'])->name('staff.maintenance.fetch');

        // Move these routes inside auth middleware
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

        Route::get('complete-profile', [StudentProfileCompleteController::class, 'index'])->name('profile.complete');
        Route::post('complete-profile/store', [StudentProfileCompleteController::class, 'store'])->name('profile.store');

        // Move payment routes inside auth middleware
        Route::post('/student/payment/upload', [StudentPaymentController::class, 'payInvoice'])->name('student.invoice.pay');
        Route::post('/student/invoice/detail', [StudentPaymentController::class, 'getInvoiceDetails'])->name('student.payment.info');
        Route::post('/student/invoice/add', [StudentPaymentController::class, 'addInvoice'])->name('student.payment.add');
    });

    // Logout Route
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});

// Keep only non-auth routes outside
Route::post('upload', UploadController::class,'')->name('upload');

// Notification Routes
// Notifications
Route::prefix('notifications')->group(function () {
    Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllAsRead');
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AccountActivationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\AdminHomeController;






// Authenication Routes

Route::get('/', [LoginController::class,'showLoginPage']); 
Route::get('/login', [LoginController::class,'showLoginPage'])->name('login'); 
Route::post('/login',[LoginController::class,'login'])->name('login');

Route::get('/register', [RegisterController::class,'showRegisterPage'])->name('register'); 
Route::post('/register',[RegisterController::class,'register'])->name('register');
Route::get('/activate-account/{token}', [AccountActivationController::class, 'activate'])->name('activate-account');

Route::get('/activate-account/{token}', [AccountActivationController::class, 'activate'])->name('activate-account');




Route::get('password/reset', [PasswordResetController::class, 'showResetRequestForm'])->name('password.request');
Route::post('password/reset', [PasswordResetController::class, 'requestResetPassword'])->name('password.email');
Route::get('password/reset/{token}', [PasswordResetController::class, 'showUpdatePasswordPage'])->name('password.reset');
Route::post('password/reset/{token}', [PasswordResetController::class, 'resetPassword'])->name('password.update');

Route::get('/admin/home',[AdminHomeController::class,'showHomePage'])->name('admin.home');


Route::get('/welcome',function(){
    return view('welcome');
});


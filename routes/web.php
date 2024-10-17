<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;


// Authenication Routes

Route::get('/', [LoginController::class,'showLoginPage']); 
Route::get('/login', [LoginController::class,'showLoginPage'])->name('login'); 
Route::post('/login',[LoginController::class,'login'])->name('login');

Route::get('/register', [RegisterController::class,'showRegisterPage'])->name('register'); 
Route::post('/register',[RegisterController::class,'register'])->name('register');


Route::get('/welcome',function(){
    return view('welcome');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Authenication Routes

Route::get('/', [LoginController::class,'showLoginPage']); 
Route::get('/login', [LoginController::class,'showLoginPage'])->name('login'); 
Route::post('/login',[LoginController::class,'login'])->name('login');

Route::get('/welcome',function(){
    return view('welcome');
});
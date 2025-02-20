<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\AccountSettings\AccountController;
use App\Http\Controllers\Api\TestApiController;
use App\Http\Controllers\Api\AccountSettings\SecurityController;
use App\Http\Controllers\Api\User\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//social login
Route::controller(GoogleController::class)->group(function () {
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});


//authentication
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::post('/otp-login', [LoginController::class, 'otpLogin']);
Route::post('/verifyuser-otp', [LoginController::class, 'verifyuserOtp']);


Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);


Route::middleware('auth:api')->group(function () {
    //middleware test
    Route::get('/profile',[UserController::class, 'userProfile'])->name('userProfile');
    Route::post('/change-password', [AccountController::class, 'changePassword']);
    Route::post('change-profile', [AccountController::class, 'changeProfile']);
    //logout devices session
    Route::post('/list/active-devices', [SecurityController::class, 'listActiveDevices']);
    Route::post('/logoutsingle-device/{id}', [SecurityController::class, 'logoutSingleDevice']);
    Route::post('/logoutall-devices', [SecurityController::class, 'logoutAllDevices']);
    //logout
    Route::post('logout', [LoginController::class, 'logout']);



});

Route::get('test', [TestApiController::class, 'index']);

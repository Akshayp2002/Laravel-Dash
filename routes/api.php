<?php

use App\Enum\Permission\User;
use App\Enum\Permission\UserEnum;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\AccountSettings\AccountController;
use App\Http\Controllers\Api\TestApiController;
use App\Http\Controllers\Api\AccountSettings\SecurityController;
use App\Http\Controllers\Api\Auth\TwoFactorAuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\User\UserController;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//social login
Route::controller(GoogleController::class)->group(function () {
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});


//Authentication
Route::post('register', [RegisterController::class, 'register']);
Route::get('/email-verify', [RegisterController::class, 'emailVerify'])->name('emailVerify');
Route::post('login', [LoginController::class, 'login']);
Route::post('/otp-login', [LoginController::class, 'otpLogin']);
Route::post('/verifyuser-otp', [LoginController::class, 'verifyuserOtp']);

// Forgot Password
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
// MFA Verify / Recovery
Route::post('/2fa/verify-authenticator-login', [TwoFactorAuthController::class, 'verifyAuthenticatorLogin']);
Route::post('/2fa/verify-recovery', [TwoFactorAuthController::class, 'verifyRecoveryCode']);
Route::get('/2fa/select-multi-mfa/{token?}/{method?}', [LoginController::class, 'selectMultiMfa']);

Route::middleware('auth:api')->group(function () {
    // MFA Authenticator Enable / Disable
    Route::get('/2fa/status', [TwoFactorAuthController::class, 'status']);
    Route::post('/2fa/enable2FA', [TwoFactorAuthController::class, 'toggle2FA'])->name('2fa.enable');
    Route::post('/2fa/disable2FA', [TwoFactorAuthController::class, 'toggle2FA'])->name('2fa.disable');
    Route::post('/2fa/enable-authenticator', [TwoFactorAuthController::class, 'enableAuthenticator']);
    Route::post('/2fa/verify-authenticator', [TwoFactorAuthController::class, 'verifyAuthenticator']);
    Route::post('/2fa/disable-authenticator', [TwoFactorAuthController::class, 'disableAuthenticator']);
    //MFA Email Otp
    Route::post('/2fa/enable-emailOtp', [TwoFactorAuthController::class, 'toggleEmailOtp'])->name('emailOtp.enable');
    Route::post('/2fa/disable-emailOtp', [TwoFactorAuthController::class, 'toggleEmailOtp'])->name('emailOtp.disable');
    // Authenticator Recovery Codes
    Route::get('/2fa/recovery-codes', [TwoFactorAuthController::class, 'getRecoveryCodes']);
    Route::get('/2fa/regenerate-recovery', [TwoFactorAuthController::class, 'regenerateRecoveryCodes']);
});

Route::middleware(['auth:api'])->group(function () {
    //middleware test
    Route::get('/profile', [UserController::class, 'userProfile'])->name('userProfile');
    Route::post('/change-password', [AccountController::class, 'changePassword']);
    Route::post('change-profile', [AccountController::class, 'changeProfile']);
    //logout devices session
    Route::post('/storedevices', [SecurityController::class, 'storeLogIndevices']);
    Route::get('/list/active-devices', [SecurityController::class, 'listActiveDevices']);
    Route::post('/logoutsingle-device', [SecurityController::class, 'logoutSingleDevice']);
    Route::post('/logoutall-devices', [SecurityController::class, 'logoutAllDevices']);
    //logout
    Route::post('logout', [LoginController::class, 'logout']);
    //userpassword-reset
    Route::post('/password-reset', [ForgotPasswordController::class, 'passwordReset']);

});

Route::middleware(['auth:api', InitializeTenancyByRequestData::class])->group(function () {
    //Transactions
    Route::get('/transactions', [TransactionController::class, 'transactions']);
});

Route::get('test', [TestApiController::class, 'index']);
Route::get('permissions', [TestApiController::class, 'permissions']);

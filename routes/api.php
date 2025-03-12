<?php

use App\Enum\Permission\User;
use App\Enum\Permission\UserEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountSettings\AccountController;
use App\Http\Controllers\Api\TestApiController;
use App\Http\Controllers\Api\AccountSettings\SecurityController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Authorization\PermissionController;
use App\Http\Controllers\Api\Authorization\RoleController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\User\UserController;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\MfaController;
use App\Http\Controllers\Api\Auth\ScreenLockController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//social login
Route::controller(SocialLoginController::class)->group(function () {
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});

Route::controller(AuthController::class)->group(function () {
    //Authentication
    Route::post('register', 'register');
    Route::get('/email-verify', 'emailVerify')->name('emailVerify');
    Route::post('login', 'login');
    Route::post('/otp-login', 'otpLogin');
    Route::post('/verifyuser-otp', 'verifyuserOtp');

    // Forgot Password
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/reset-password', 'resetPassword');

    //MFA method select
    Route::get('/2fa/select-multi-mfa/{token?}/{method?}', 'selectMultiMfa');
});

// MFA Verify / Recovery
Route::post('/2fa/verify-authenticator-login', [MfaController::class, 'verifyAuthenticatorLogin']);
Route::post('/2fa/verify-recovery', [MfaController::class, 'verifyRecoveryCode']);


// Verify
Route::middleware('auth:api')->group(function () {
    Route::post('/verify-screen-lock', [ScreenLockController::class, 'verifyLock']);
});

Route::middleware(['auth:api', 'screen.lock'])->group(function () {
    //Enable Disable Screen Lock
    Route::get('/enable-screen-lock', [ScreenLockController::class, 'enableLock']);
    Route::get('/disable-screen-lock', [ScreenLockController::class, 'disableLock']);
    Route::post('/setup-screen-lock', [ScreenLockController::class, 'setupLock']);
    // MFA Authenticator Enable / Disable
    Route::get('/2fa/status', [MfaController::class, 'status']);
    Route::post('/2fa/enable2FA', [MfaController::class, 'toggle2FA'])->name('2fa.enable');
    Route::post('/2fa/disable2FA', [MfaController::class, 'toggle2FA'])->name('2fa.disable');
    Route::post('/2fa/enable-authenticator', [MfaController::class, 'enableAuthenticator']);
    Route::post('/2fa/verify-authenticator', [MfaController::class, 'verifyAuthenticator']);
    Route::post('/2fa/disable-authenticator', [MfaController::class, 'disableAuthenticator']);
    //MFA Email Otp
    Route::post('/2fa/enable-emailOtp', [MfaController::class, 'toggleEmailOtp'])->name('emailOtp.enable');
    Route::post('/2fa/disable-emailOtp', [MfaController::class, 'toggleEmailOtp'])->name('emailOtp.disable');
    Route::post('/2fa/enable-emailOtp-verify', [MfaController::class, 'toggleEmailOtpVerify'])->name('emailOtpVerify.enable');
    Route::post('/2fa/disable-emailOtp-verify', [MfaController::class, 'toggleEmailOtpVerify'])->name('emailOtpVerify.disable');

    // Authenticator Recovery Codes
    Route::get('/2fa/recovery-codes', [MfaController::class, 'getRecoveryCodes']);
    Route::get('/2fa/regenerate-recovery', [MfaController::class, 'regenerateRecoveryCodes']);

    // Roles Create
    Route::get('role-create', [RoleController::class, 'create']);
    Route::post('role-store', [RoleController::class, 'store']);
    Route::get('role-edit/{id}', [RoleController::class, 'edit']);
    Route::put('role-update/{id}', [RoleController::class, 'update']);

    //middleware test
    Route::get('/profile', [UserController::class, 'userProfile'])->name('userProfile');
    Route::post('/change-password', [AccountController::class, 'changePassword']);
    Route::post('change-profile', [AccountController::class, 'changeProfile']);
    //logout devices session
    Route::post('/storedevices', [SecurityController::class, 'storeLogIndevices']);
    Route::get('/list/active-devices/{num?}', [SecurityController::class, 'listActiveDevices']);
    Route::post('/logoutsingle-device', [SecurityController::class, 'logoutSingleDevice']);
    Route::post('/logoutall-devices', [SecurityController::class, 'logoutAllDevices']);

    Route::controller(AuthController::class)->group(function () {
        //logout
        Route::post('logout', 'logout');

        //userpassword-reset
        Route::post('/password-reset', 'passwordReset');
    });
});



Route::middleware(['auth:api', InitializeTenancyByRequestData::class])->group(function () {
    //Transactions
    Route::get('/transactions', [TransactionController::class, 'transactions']);
});

Route::get('test', [TestApiController::class, 'index']);
Route::get('permissions', [TestApiController::class, 'permissions']);
Route::get('service', [PermissionController::class, 'service']);

Route::get('/version', function () {
    // For checking code update in server change the version
    return "0.1.2";
});

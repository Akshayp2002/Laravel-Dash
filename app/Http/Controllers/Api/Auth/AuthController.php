<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\EmailRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;

class AuthController extends Controller
{

    // protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //register
    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request->validated());
    }

    //verify user email
    public function emailVerify(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required'
        ]);
        return $this->authService->emailVerify($validated);
    }

    //Basic login
    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    //multiple mfa verify
    public function selectMultiMfa($token, $method)
    {
        return $this->authService->selectMultiMfa($token, $method);
    }

    // OTP Login
    public function otpLogin(EmailRequest $request)
    {
        return $this->authService->otpLogin($request->validated());
    }

    // Verify OTP for OTP login
    public function verifyuserOtp(VerifyOtpRequest $request)
    {
        return $this->authService->verifyuserOtp($request->validated());
    }

    //forgot password
    public function forgotPassword(EmailRequest $request)
    {
        return $this->authService->forgotPassword($request->validated());
    }

    //verifyOtp
    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->authService->verifyOtp($request->validated());
    }

    //resetPassword
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'user_token' => 'required',
            'password'   => 'required|string|min:8|confirmed',
        ]);
        return $this->authService->resetPassword($validated);
    }

    //passwordReset
    public function passwordReset()
    {
        return $this->authService->passwordReset();
    }

    // Logout
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }

}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Api\Auth\MfaService;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\PasswordRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;

class MfaController extends Controller
{
    public function __construct(MfaService $mfaService)
    {
        $this->mfaService = $mfaService;
    }

    // check if the user is enabled
    public function status()
    {
        return $this->mfaService->status();
    }

    //enable/disable 2fa
    public function toggle2FA(PasswordRequest $request)
    {
        return $this->mfaService->toggle2FA($request->validated());
    }

    // Enable 2fa
    public function enableAuthenticator(PasswordRequest $request)
    {
        return $this->mfaService->enableAuthenticator($request->validated());
    }

    //Verify the 2fa initally
    public function verifyAuthenticator(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);
        return $this->mfaService->verifyAuthenticator($request);
    }

    // Verify MFA Code for while /login
    public function verifyAuthenticatorLogin(Request $request)
    {
        $request->validate([
            'mfa_token' => 'required',
            'code'      => 'required|numeric'
        ]);
        return $this->mfaService->verifyAuthenticatorLogin($request);
    }


    //Recovery Code Generate
    public function getRecoveryCodes(){
        return $this->mfaService->getRecoveryCodes();
    }

    // Regenerate Recovery Code
    public function regenerateRecoveryCodes(){
        return $this->mfaService->regenerateRecoveryCodes();
    }

    // Verify recovery code during login
    public function  verifyRecoveryCode(Request $request){
        $request->validate([
            'mfa_token'     => 'required',
            'recovery_code' => 'required|string',
        ]);
        return $this->mfaService->verifyRecoveryCode($request);
    }

    // Disable 2FA
    public function disableAuthenticator(PasswordRequest $request)
    {
        return $this->mfaService->disableAuthenticator($request->validated());
    }

    // enable/disable mfa email otp
    public function toggleEmailOtp(Request $request){
        return $this->mfaService->toggleEmailOtp($request);
    }

    // email otp verify
    public function toggleEmailOtpVerify(VerifyOtpRequest $request) {
        return $this->mfaService->toggleEmailOtpVerify($request->validated());
    }
}

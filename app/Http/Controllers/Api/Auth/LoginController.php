<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\OtpHelper;
use App\Helpers\RateLimiterHelper;
use App\Mail\OtpLogin;
use App\Models\Otp;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
            'remember' => 'in:true,false',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        // Rate Limiting for 5 request in a minute
        if ($response = RateLimiterHelper::checkLoginRateLimit($request->email)) return $response;

        if (Auth::attempt($request->only('email', 'password'), $request->remember ?? false)) {
            $user = Auth::user();

            // if email is not verified
            if (!$user->email_verified_at) {
                return response()->json(['message' => 'Please verify your email to login!'], 400);
            }

            // Check if Two-Factor Authentication (2FA) is enabled
            if ($user->twoFactorStatus && $user->twoFactorStatus->two_factor_all_status) {
                $twoFactor = $user->twoFactorStatus;

                // Collect enabled 2FA methods
                $enabledMethods = [];

                if ($twoFactor->qr_code_status) {
                    $enabledMethods[] = 'qr';
                }
                if ($twoFactor->email_otp_status) {
                    $enabledMethods[] = 'email';
                }

                // If no specific 2FA method is enabled, proceed with normal login
                if (empty($enabledMethods)) {
                    goto normalLogin;
                }

                // for single mfa enabled
                if($twoFactor->qr_code_status && !$twoFactor->email_otp_status){
                    $result = OtpHelper::generateToken($user->id);
                    return response()->json([
                        'message'             => 'Two-Factor Authentication required!',
                        'two_fa_enabled'      => true,
                        'mfa_token' => $result['token'],
                    ], 200);
                }

                if (!$twoFactor->qr_code_status && $twoFactor->email_otp_status) {
                    $otp = OtpHelper::generateOtp($user->id);
                    $emailSent = Mail::to($user->email)->send(new OtpLogin($otp));
                    if ($emailSent) {
                        return response()->json([
                            'message'        => 'OTP sent successfully.',
                            'two_fa_enabled' => true,
                            'user_token'     => $otp['user_token']
                        ],      200);
                    } else {
                        return response()->json(['message' => 'Failed to send OTP try again latter.'], 400);
                    }
                }

                // Determine response message
                $message = count($enabledMethods) > 1
                    ? 'Multiple authentication methods available!'
                    : ucfirst($enabledMethods[0]) . ' Authentication required!';

                $result = OtpHelper::generateToken($user->id);
                return response()->json([
                    'message'        => $message,
                    'two_fa_enabled' => true,
                    'mfa_token'      => $result['token'],
                    'method'         => $enabledMethods,
                ], 200);
            }

            normalLogin:
            // Proceed with normal authentication if 2FA is not enabled
            $deviceName = $request->userAgent();
            $data['name']     = $user->name;
            $data['token']    = $user->createToken($deviceName)->accessToken;
            $data['email']    = $user->email;

            return response()->json(['data' => $data, 'message' => 'Logged in successfully!'], 200);
        } else {
            return response()->json(['message' => 'Invalid Credentials!'], 400);
        }
    }

    //multiple mfa verify
    public function selectMultiMfa($token , $method){

        $multitoken = Otp::where('otp', $token)->first();
        if(!$multitoken){
            return response()->json([
                'message'        => 'Unable to authenticate.!',
            ], 400);
        }
        if($method == "qr"){
            if($multitoken){
                $result = OtpHelper::generateToken($multitoken->user_id);
                $multitoken->delete();
                return response()->json([
                    'message'        => 'Verify using authenticator app.!',
                    'two_fa_enabled' => true,
                    'mfa_token'     => $result['token'],
                ], 200);
            }
        }elseif($method == "email"){
            if ($multitoken) {
                $otp = OtpHelper::generateOtp($multitoken->user_id);
                $user = User::where('id', $multitoken->user_id)->first();
                $emailSent = Mail::to($user->email)->send(new OtpLogin($otp));
                $multitoken->delete();
                if ($emailSent) {
                    return response()->json([
                        'message'        => 'OTP sent successfully.',
                        'two_fa_enabled' => true,
                        'user_token'     => $otp['user_token']],      200);
                } else {
                    return response()->json(['message' => 'Failed to send OTP try again latter.'], 400);
                }
            }
        }
    }

    // OTP Login
    public function otpLogin(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email', // Ensure email exists in the users table
        ]);
        // Retrieve the user for the email content
        $user = User::where('email', $request->email)->first();

        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Please verify your email to login!'], 400);
        }
        // Generate OTP using the helper
        $result = OtpHelper::generateOtp($user->id);
        // Send the OTP using the mail
        $emailSent = Mail::to($user->email)->send(new OtpLogin($result));
        if ($emailSent) {
            return response()->json(['message' => 'OTP sent successfully.', 'user_token' => $result['user_token']], 200);
        } else {
            return response()->json(['message' => 'Failed to send OTP try again latter.'], 400);
        }
    }

    // Verify OTP for OTP login
    public function verifyuserOtp(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_token' => 'required',
            'otp'        => 'required|numeric',
        ]);
        $isValid = OtpHelper::verifyOtpUser($request->user_token, $request->otp);

        if ($isValid) {
            $user = User::where('id', $isValid)->first();
            // if email is not verified
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 400);
            }
            $deviceName = $request->userAgent();
            $data['name']     = $user->name;
            $data['token']    = $user->createToken($deviceName)->accessToken;
            $data['email']    = $user->email;

            return response()->json([
                'data'    => $data,
                'message' => "Logged in successfully!"
            ], 200);
        } else {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        $user = Auth::user();
        $currentToken = $request->user()->token();
        if ($currentToken) {
            $currentTokenId = $currentToken->id;
            $user->tokens()->where('id', $currentTokenId)->delete();
            return response()->json([
                'data'    => $user,
                'message' => 'user Logged out successfully!'
            ], 200);
        } else {
            return response()->json(['message' => 'User not found!'], 400);
        }
    }
}

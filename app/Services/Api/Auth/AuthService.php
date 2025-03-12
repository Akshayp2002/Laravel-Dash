<?php

namespace App\Services\Api\Auth;

use App\Http\Resources\Api\Auth\AuthResource;
use App\Services\BaseService;
use App\Helpers\RateLimiterHelper;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpLogin;
use App\Helpers\OtpHelper;
use App\Models\Otp;
use App\Mail\EmailVerified;
use Illuminate\Support\Facades\Hash;
use App\Models\DeviceSession;
use App\Mail\ForgotPasswordOtp;
use App\Mail\ResetPassword;

class AuthService extends BaseService
{
   /**
     * Register a new user and send email verification OTP.
     */
    public function register($request)
    {
        $user = User::create([
            'name'     => $request['name'],
            'email'    => $request['email'],
            'password' => Hash::make($request['password']),
        ]);
        // Email Verificatication mail with a random token
        $helper = OtpHelper::generateToken($user->id);
        Mail::to($user->email)->send(new EmailVerified($helper['token']));
        return response()->json([
            'message' => "User register successfully, Verify your Email to Login"
        ], 200);
    }

    /**
     * Verify user email using OTP.
     */
    public function emailVerify($request)
    {
        // Retrieve OTP record
        $otpRecord = Otp::where('otp', $request['token'])->first();
        if (!$otpRecord || $otpRecord->expiry > now()) {
            return redirect()->away(env('CLIENT_URL') . '?verification-expired');
        }
        // Fetch user
        $user = User::find($otpRecord->user_id);
        if (!$user) {
            return redirect()->away(env('CLIENT_URL') . '?verification-failed');
        }
        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();
        // Delete OTP after successful verification
        $otpRecord->delete();
        // Redirect to frontend client after verification
        return redirect()->away(env('CLIENT_URL') . '?verification-success');
    }

    /**
     * Handle user login with rate limiting and MFA checks.
     */
    public function login($request)
    {
        // Rate Limiting for 5 request in a minute
        if ($response = RateLimiterHelper::checkLoginRateLimit($request['email'])) return $response;

        if (!Auth::attempt(['email' => $request['email'], 'password' => $request['password']], $request['remember'] ?? false)) {
            return response()->json(['message' => 'Invalid Credentials!'], 400);
        }
        $user = Auth::user();
        // if email is not verified
        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Please verify your email to login!'], 400);
        }
        // Check if Two-Factor Authentication (2FA) is enabled
        if ($user->twoFactorStatus && $user->twoFactorStatus->two_factor_all_status) {
            return $this->handleTwoFactorAuth($user);
        }

        return response()->json([
            'data'    => new AuthResource($user),
            'message' => 'Logged in successfully!',
        ], 200);
    }

    /**
     * Handle Multi-Factor Authentication (MFA) during login.
     */
    private function handleTwoFactorAuth(User $user)
    {
        // Collect enabled 2FA methods
        $twoFactor = $user->twoFactorStatus;
        $enabledMethods = [];

        if ($twoFactor->qr_code_status) {
            $enabledMethods[] = 'qr';
        }
        if ($twoFactor->email_otp_status) {
            $enabledMethods[] = 'email';
        }

        if (empty($enabledMethods)) {
            return response()->json(['data' => new AuthResource($user), 'message' => 'Logged in successfully!'],200);
        }
        // for single mfa enabled
        if ($twoFactor->qr_code_status && !$twoFactor->email_otp_status) {
            $result = OtpHelper::generateToken($user->id);
            return response()->json([
                'message'        => 'Two-Factor Authentication required!',
                'two_fa_enabled' => true,
                'mfa_token'      => $result['token'],
            ], 200);
        }

        if (!$twoFactor->qr_code_status && $twoFactor->email_otp_status) {
            $otp = OtpHelper::generateOtp($user->id);
            $emailSent = Mail::to($user->email)->send(new OtpLogin($otp));

            return response()->json([
                'message'        => $emailSent ? 'OTP sent successfully.' : 'Failed to send OTP, try again later.',
                'two_fa_enabled' => true,
                'user_token'     => $otp['user_token'],
            ], $emailSent ? 200 : 400);
        }

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


    /**
     * Select an MFA method for verification.
     */
    public function selectMultiMfa($token, $method)
    {
        $multitoken = Otp::where('otp', $token)->first();
        if (!$multitoken) {
            return response()->json([
                'message'        => 'Unable to authenticate.!',
            ], 400);
        }
        if ($method == "qr") {
            if ($multitoken) {
                $result = OtpHelper::generateToken($multitoken->user_id);
                $multitoken->delete();
                return response()->json([
                    'message'        => 'Verify using authenticator app.!',
                    'two_fa_enabled' => true,
                    'mfa_token'     => $result['token'],
                ], 200);
            }
        } elseif ($method == "email") {
            if ($multitoken) {
                $otp       = OtpHelper::generateOtp($multitoken->user_id);
                $user      = User::where('id', $multitoken->user_id)->first();
                $emailSent = Mail::to($user->email)->send(new OtpLogin($otp));
                $multitoken->delete();
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
        }
    }

    /**
     * Handle OTP login.
     */
    public function otpLogin($request)
    {

        // Retrieve the user for the email content
        $user = User::where('email', $request['email'])->first();
        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Please verify your email to login!'], 400);
        }
        // Generate OTP using the helper
        $result = OtpHelper::generateOtp($user->id);
        // Send the OTP using the mail
        $emailSent = Mail::to($user->email)->send(new OtpLogin($result));
        return response()->json([
            'message'        => $emailSent ? 'OTP sent successfully.' : 'Failed to send OTP, try again later.',
            'two_fa_enabled' => true,
            'user_token'     => $result['user_token'],
        ], $emailSent ? 200 : 400);
    }

    /**
     * Verify OTP for login.
     */
    public function verifyuserOtp($request)
    {
        $isValid = OtpHelper::verifyOtpUser($request['user_token'], $request['otp']);
        if ($isValid) {
            $user = User::where('id', $isValid)->first();
            // if email is not verified
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 400);
            }
            return response()->json([
                'data'    => new AuthResource($user),
                'message' => "Logged in successfully!"
            ], 200);
        } else {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }
    }

    /**
     *  user forgotPassword
     */
    public function forgotPassword($request)
    {
        // Retrieve the user for the email content
        $user = User::where('email', $request['email'])->first();
        // Rate Limiting for 5 request in a minute
        if ($response = RateLimiterHelper::checkLoginRateLimit($request['email'])) return $response;
        // Generate OTP using the helper
        $result = OtpHelper::generateOtp($user->id);
        // Send the OTP using the email
        $emailSent = Mail::to($user->email)->send(new ForgotPasswordOtp($result));
        return response()->json([
            'message'        => $emailSent ? 'OTP sent successfully.' : 'Failed to send OTP, try again later.',
            'user_token'     => $result['user_token'],
        ], $emailSent ? 200 : 400);
    }

    /**
     *  verify Otp for forgot password
     */
    public function verifyOtp($request)
    {
        $tokenUser = OtpHelper::verifyOtpUser($request['user_token'], $request['otp']);
        if ($tokenUser) {
            $result = OtpHelper::generateToken($tokenUser);
            return response()->json(['message' => 'OTP is valid. Proceed with password reset.', 'user_token' => $result['token']], 200);
        } else {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }
    }

    /**
     *  resetting the password - [forgot password reset and user password rest]
     */
    public function resetPassword($request)
    {
        // Retrieve the most recent OTP record associated with user_token
        $veriryUser = Otp::where('otp', $request['user_token'])->latest()->first();
        // Check if OTP record exists and is valid
        if (!$veriryUser || $veriryUser->expires_at < now()) {
            return response()->json(['message' => 'User Session is invalid or expired.'], 400);
        }
        $user = User::where('id', $veriryUser->user_id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 400);
        }
        // Reset the password
        $user->password = Hash::make($request['password']);
        $user->save();
        // Delete the OTP record since it's no longer needed
        $veriryUser->delete();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }

    /**
     *  password reset , for resetting the password use above resetpassword function
     */
    public function passwordReset()
    {
        $user = auth()->user();
        // Generate token using the helper
        $result = OtpHelper::generateToken($user->id);
        $resetUrl = env('CLIENT_URL') . '?verification-success';
        // Send the OTP using the email
        Mail::to($user->email)->send(new ResetPassword($resetUrl));
        return response()->json([
            'user_token' => $result['token'],
            'message' => "Reset link sent successfully, Reset your password"
        ], 200);
    }

    /**
     * Logout the user .
     */
    public function logout($request)
    {
        $user = Auth::user();
        $currentToken = $request->user()->token();
        if ($currentToken) {
            $deviceSession = DeviceSession::where('user_id', $user->id)
                ->where('access_token', $currentToken->id)
                ->first();
            $deviceSession->delete();
            $user->tokens()->where('id', $currentToken->id)->delete();
            return response()->json([
                'data'    => $user,
                'message' => 'user Logged out successfully!'
            ], 200);
        } else {
            return response()->json(['message' => 'User not found!'], 400);
        }
    }
}

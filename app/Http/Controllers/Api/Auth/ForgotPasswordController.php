<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\OtpHelper;
use App\Helpers\RateLimiterHelper;
use App\Mail\ForgotPasswordOtp;
use App\Models\Otp;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;

class ForgotPasswordController extends Controller
{

    public function forgotPassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email', // Ensure email exists in the users table
        ]);
        // Retrieve the user for the email content
        $user = User::where('email', $request->email)->first();
        // Rate Limiting for 5 request in a minute
        if ($response = RateLimiterHelper::checkLoginRateLimit($request->email)) return $response;

        // Generate OTP using the helper
        $result = OtpHelper::generateOtp($user->id);
        // Send the OTP using the email
        $emailSent = Mail::to($user->email)->send(new ForgotPasswordOtp($result));

        // $emailSent = EmailHelper::sendEmail($user->email, $subject, $view, $data);
        if ($emailSent) {
            return response()->json(['message' => 'OTP sent successfully.', 'user_token' => $result['user_token']], 200);
        } else {
            return response()->json(['message' => 'Failed to send OTP.'], 400);
        }
    }


    public function verifyOtp(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_token' => 'required',
            'otp'        => 'required|numeric',
        ]);
        $isValid = OtpHelper::verifyOtp($request->user_token, $request->otp);

        if ($isValid) {
            return response()->json(['message' => 'OTP is valid. Proceed with password reset.', 'user_token' => $request->user_token], 200);
        } else {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }
    }

    public function resetPassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_token' => 'required',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        // Retrieve the most recent OTP record associated with user_token
        $veriryUser = Otp::where('id', $request->user_token)->latest()->first();
        // Check if OTP record exists and is valid
        if (!$veriryUser || $veriryUser->expires_at < now()) {
            return response()->json(['message' => 'OTP is invalid or expired.'], 400);
        }
        $user = User::where('id', $veriryUser->user_id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 400);
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->save();
        // Delete the OTP record since it's no longer needed
        $veriryUser->delete();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }

    // user reset password
    public function passwordReset()
    {
        $user = auth()->user();
        // Generate token using the helper
        $result = OtpHelper::generateOtp($user->id);
        $resetUrl = env('CLIENT_URL') . '?verification-success';
        // Send the OTP using the email
        Mail::to($user->email)->send(new ResetPassword($resetUrl));
        return response()->json([
            'user_token' => $result['user_token'],
            'message' => "Reset link sent successfully, Reset your password"
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\OtpHelper;
use App\Helpers\EmailHelper;
use App\Models\Otp;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

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

        // Generate OTP using the helper
        $result = OtpHelper::generateOtp($user->id);

        // Prepare the data for the email
        $subject = "Your OTP Code for Password Reset";
        $view    = 'emails.Api.Auth.forgotPasswordOtp';

        // Retrieve the user for the email content (optional)
        $data = [
            'otp'        => $result['otp'],
            'expires_at' => now()->addMinutes(5)->format('Y-m-d H:i:s'),
        ];

        // Send the OTP using the helper
        $emailSent = EmailHelper::sendEmail($user->email, $subject, $view, $data);
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
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->save();
        // Delete the OTP record since it's no longer needed
        $veriryUser->delete();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }
}

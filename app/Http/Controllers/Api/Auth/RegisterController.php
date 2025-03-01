<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\OtpHelper;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerified;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Email Verificatication mail with a random token
        $helper = OtpHelper::generateToken($user->id);

        Mail::to($request->email)->send(new EmailVerified($helper['token']));

        return response()->json([
            'message' => "User register successfully, Verify your Email to Login"
        ]);
    }

    public function emailVerify(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        // Retrieve OTP record
        $otpRecord = Otp::where('otp', $request->token)->first();

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
}

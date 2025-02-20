<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\OtpHelper;
use App\Helpers\EmailHelper;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'in:true,false',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'data' => [],
                    'message' => $validator->errors()
                ]
            );
        }
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        $remember = $request->has('remember') ? $request->remember : false;
        if (Auth::attempt($credentials, $remember)) {
            $deviceName = $request->userAgent();
            $user             = Auth::user();
            $data['name']     = $user->name;
            $data['token']    = $user->createToken($deviceName)->accessToken;
            $data['email']    = $user->email;
            $data['remember'] = $request->remember;

            return response()->json([
                'data' => $data,
                'message' => "Logged in!"
            ]);
        } else {
            return response()->json([
                'data' => [],
                'message' => "invalid Credentials!"
            ]);
        }
    }

    public function otpLogin(Request $request)
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
        $view    = 'emails.Api.Auth.otpLogin';

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

    public function verifyuserOtp(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_token' => 'required',
            'otp'        => 'required|numeric',
        ]);
        $isValid = OtpHelper::verifyOtpUser($request->user_token, $request->otp);

        if ($isValid) {
            // Retrieve the most recent OTP record associated with user_token
            // $veriryUser = Otp::where('id', $request->user_token)->latest()->first();
            // Check if OTP record exists and is valid
            $user = User::where('id', $isValid)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }
            $data['name']     = $user->name;
            $data['token']    = $user->createToken('MyApp')->accessToken;
            $data['email']    = $user->email;
            $data['remember'] = $request->remember;

            return response()->json([
                'data' => $data,
                'message' => "Logged in!"
            ]);
        } else {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }
    }

    public function logout(Request $request){

        $user = auth()->user();
        $currentToken = $request->user()->token();
        if ($currentToken) {
            $currentTokenId = $currentToken->id;
            $user->tokens()->where('id',$currentTokenId)->delete();
        }
        return response()->json([
            'data' => $user,
            'message' => 'user Logged out successfully.'
        ]);
    }
}

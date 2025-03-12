<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()

    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if a user already exists with this email
        $user = User::where('email', $googleUser->email)->first();
        if($user){
            if(!$user->google_id){
                $user->update([
                    'google_id' => $googleUser->id,
                ]);
            }
        }else{
            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name'      => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password'  => bcrypt(uniqid()),
            ]);
        }


        // Generate a Laravel Passport token
        $token = $user->createToken('GoogleAuthToken')->accessToken;
        $frontendUrl = env('CLIENT_URL');

        // temporary code for development
        try {
            $response = Http::timeout(5)->get($url = 'http://localhost:5628');
            if ($response->successful()) {
                $frontendUrl = $url;
            }
        } catch (Exception $e) {
            $frontendUrl = env('CLIENT_URL');
        }

        if($token){
            return redirect()->to("{$frontendUrl}/auth/callback?token={$token}");
        }else{
            return response()->json(['message' => 'User not logged in!.'], 400);
        }
    }
}

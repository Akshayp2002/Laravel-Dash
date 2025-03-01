<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userProfile(){
        $user = Auth::user()->only(['id','name','email', 'profile_photo_url']);
        return response()->json([
            'data'    => $user,
            'message' => 'User Profile'
        ]);
    }
}

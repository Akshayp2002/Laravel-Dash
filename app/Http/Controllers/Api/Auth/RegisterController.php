<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'data' => [],
                    'message' => $validator->errors()
                ]
            );
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user['token'] =  $user->createToken('authtoken')->accessToken;
        $user['name'] =  $user->name;
        return response()->json([
            'data' => $user,
            'message' => "User register successfully."
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\AccountSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SecurityController extends Controller
{

    // store the loogedin devices
    public function storeLogIndevices(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'app_name'     => 'required|string',
            'os'           => 'required|string',
            'ip_address'   => 'required|ip',
            'latitude'     => 'string',
            'longitude'    => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $tokenId = $request->user()->token()->id;
        $deviceSession = DeviceSession::create([
            'user_id'        => auth()->id(),
            'device_token'   => Str::upper(Str::random(8)),
            'app_name'       => $request->app_name,
            'os'             => $request->os,
            'ip_address'     => $request->ip_address,
            'access_token'   => $tokenId,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'last_active_at' => now(),
        ]);

        return response()->json([
            'device_session' => $deviceSession,
            'message'        => 'Device session stored successfully!',
        ], 200);
    }


    // list all logged in devices
    public function listActiveDevices()
    {
        $user       = auth()->user();
        $tfa_status = filled($user->two_factor_confirmed_at);

        $active_devices =  DeviceSession::where('user_id', $user->id)->get();
        if ($active_devices) {
            return response()->json([
                'active_devices' => $active_devices,
                'tfa_status'     => $tfa_status,
                'message'        => 'Fetched active devices successfully.'
            ], 200);
        } else {
            return response()->json(['message' => 'No active device found.'], 400);
        }
    }



    // logout single device
    public function logoutSingleDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:device_sessions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $user = auth()->user();
        // Find the device session
        $deviceSession = DeviceSession::where('user_id', $user->id)
            ->where('id', $request->id)
            ->first();
        if (!$deviceSession) {
            return response()->json(['message' => 'Device not found or already logged out.'], 400);
        }
        $user->tokens()->where('id', $deviceSession->access_token)->delete();
        // Delete the device session
        $deviceSession->delete();
        return response()->json([
            'message' => 'Device logged out successfully!',
        ], 200);
    }



    // logout from all other devices
    public function logoutAllDevices(Request $request)
    {
        $user = auth()->user();
        $currentToken = $request->user()->token();
        if ($currentToken) {

            // delete all tokens except the current one
            $currentTokenId = $currentToken->id;
            $user->tokens()->whereNot('id', $currentTokenId)->delete();

            // Delete all device sessions except the current one
            DeviceSession::where('user_id', $user->id)
            ->whereNot('access_token', $currentTokenId)
            ->delete();

            return response()->json([
                'message' => 'Logged out from all other devices!',
            ], 200);
        }else{
            return response()->json([
                'message' => 'User not logged in!',
            ], 400);
        }

    }

}

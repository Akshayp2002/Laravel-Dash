<?php

namespace App\Http\Controllers\Api\AccountSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;

class SecurityController extends Controller
{
    public function listActiveDevices(){

        $user = auth()->user();
        $active_devices = $user->tokens()->get(['id', 'name', 'created_at']);
        if($active_devices){
                    return response()->json([
                        'active_devices' => $active_devices,
                        'message' => 'Fetched active devices successfully.'
                    ]);
        }else{
            return response()->json([
                'active_devices' => [],
                'message' => 'No active device found.'
            ]);
        }

    }

    public function logoutSingleDevice($id)
    {
        $user = auth()->user();
        $device = $user->tokens()->find($id);
        if ($device) {
            $deviceName = $device->name;
            $device->delete();
            return response()->json([
                'device' => $deviceName,
                'message' => "Logged out from the device successfully."
            ]);
        }
        return response()->json([
            'data' => [],
            'message' => 'Device not found!'
        ], 404);
    }


    public function logoutAllDevices(Request $request)
    {

        $user = auth()->user();
        $currentToken = $request->user()->token();
        if ($currentToken) {
            $currentTokenId = $currentToken->id;
            $user->tokens()->whereNot('id',$currentTokenId)->delete();
        }
        $currentSessionId = session()->getId();
        if ($currentSessionId) {
            Session::where('user_id', $user->id)
                ->whereNot('id',$currentSessionId)
                ->delete();
        }
        return response()->json([
            'data' => $user,
            'message' => 'Logged out from all devices successfully.'
        ]);
    }
}

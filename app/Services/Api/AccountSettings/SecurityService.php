<?php

namespace App\Services\Api\AccountSettings;

use App\Models\DeviceSession;
use App\Services\BaseService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SecurityService extends BaseService
{
    // store the loogedin devices
    public function storeLogIndevices($request)
    {
        DeviceSession::updateOrCreate(
            ['access_token' => $request->user()->token()->id],
            [
                'user_id'        => auth()->id(),
                'device_token'   => Str::upper(Str::random(8)),
                'app_name'       => $request->app_name,
                'os'             => $request->os,
                'ip_address'     => $request->ip_address,
                'latitude'       => $request->latitude,
                'longitude'      => $request->longitude,
                'location'       => $this->getDeviceLocation($request->latitude, $request->longitude),
                'last_active_at' => now(),
            ]
        );
        return response()->json([
            'message'        => 'Device session stored successfully!',
        ], 200);
    }

    private function getDeviceLocation($latitude = null, $longitude = null)
    {
        // Skip the API request if coordinates are invalid (e.g., 0.0 or null)
        if (is_null($latitude) || is_null($longitude) || $latitude == 0.0 || $longitude == 0.0) {
            return 'Unknown';
        }
        // Call the Nominatim API to get the location name
        try {
            $response = Http::withHeaders([
                'User-Agent' => "Rugr/0.1 (developer@rugr.com)", // Make sure to provide a valid email or app name
            ])->get("https://nominatim.openstreetmap.org/reverse", [
                'lat'    => $latitude,
                'lon'    => $longitude,
                'format' => 'json',
            ]);
            // Check if the response is successful and contains address data
            if ($response->successful() && isset($response->json()['address'])) {
                $locationData = $response->json()['address'];

                // Get the location data with fallback options for missing fields
                $city  = $locationData['city'] ?? $locationData['state_district'] ?? $locationData['suburb'] ?? 'Unknown City';
                $state = $locationData['state'] ?? 'Unknown State';
                return $city . ', ' . $state;
            } else {
                // In case of invalid response, set a default value
                return 'Not found';
            }
        } catch (\Exception $e) {
            // Catch any connection issues or API errors
            return 'Not found';
        }
    }

    // list all logged in devices
    public function listActiveDevices($request)
    {
        // Get the optional 'num' parameter from the request, default to null (meaning no limit)
        return response()->json([
            'active_devices' => $this->activelist(Auth::user()->id, $request->input('num')),
            'message'        => 'Fetched active devices successfully.'
        ], 200);
    }
    // get active user list with userId
    function activelist($userId, $num = null)
    {
        // Start the query to retrieve active devices
        $query = DeviceSession::where('user_id', $userId)->orderBy('last_active_at', 'desc');
        // If num is provided, limit the number of devices
        if ($num) {
            $query = $query->limit($num);  // Limit the results to the provided number
        }
        // Execute the query and fetch the devices
        $active_devices = $query->get();

        // Prepare the device details
        $device_details = [];
        foreach ($active_devices as $deviceSession) {
            // Prepare the device details to be added to the response
            $device_details[] = [
                'id'             => $deviceSession->id,
                'app_name'       => $deviceSession->app_name,
                'os'             => $deviceSession->os,
                'ip_address'     => $deviceSession->ip_address,
                'latitude'       => $deviceSession->latitude,
                'longitude'      => $deviceSession->longitude,
                'location'       => $deviceSession->location,
                'last_active_at' => Carbon::parse($deviceSession->last_active_at)->format('M d \a\t h:i A'), // Format: Oct 24 at 3:15 AM
                'os_image'       => $this->getDeviceImage('os', $deviceSession->os),
                'app_image'      => $this->getDeviceImage('browsers', $deviceSession->app_name),
            ];
        }
        return $device_details;
    }
    // Create a function to return the image URL based on os or app_name
    private function getDeviceImage($type, $name)
    {
        // Set the folder based on type (os or browser)
        $folder = $type === 'os' ? 'os' : 'browsers';
        $name   = strtolower($name);
        // Construct the image path (make sure the name matches the image names stored in the public folder)
        return url("assets/{$folder}/{$name}.png");
    }


    // logout single device
    public function logoutSingleDevice($request)
    {
        $user = Auth::user();
        // Find the requested device session
        $deviceSession = DeviceSession::where('user_id', $user->id)
            ->where('id', $request->id)
            ->firstOrFail();

        // Prevent logging out from the current device
        if ($deviceSession->access_token == $request->user()->token()->id) {
            return response()->json(['message' => 'You cannot log out from the current device.'], 403);
        }

        // Revoke the token and delete the session
        $user->tokens()->where('id', $deviceSession->access_token)->delete();
        $deviceSession->delete();

        return response()->json([
            'message'        => 'Device logged out successfully.',
            'active_devices' => $this->activelist($user->id, null)
        ], 200);
    }

    // logout from all other devices
    public function logoutAllDevices($request)
    {
        $user         = auth()->user();
        $currentToken = $request->user()->token();
        if (!$currentToken) {
            return response()->json([
                'message' => 'User not logged in!',
            ], 400);
        }
        // delete all tokens except the current one
        $user->tokens()->whereNot('id', $currentToken->id)->delete();
        // Delete all device sessions except the current one
        DeviceSession::where('user_id', $user->id)
            ->whereNot('access_token', $currentToken->id)
            ->delete();

        return response()->json([
            'message' => 'Logged out from all other devices!',
        ], 200);
    }
}

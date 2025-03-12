<?php

namespace App\Http\Controllers\Api\AccountSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AccountSettings\LogoutSingleDeviceRequest;
use App\Http\Requests\Api\AccountSettings\StoreLoginDeviceRequest;
use Illuminate\Http\Request;
use App\Services\Api\AccountSettings\SecurityService;

class SecurityController extends Controller
{
    /**
     * Create a new class instance.
     *
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Store details of the logged-in device.
     *
     * @param StoreLoginDeviceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeLogIndevices(StoreLoginDeviceRequest $request)
    {
        return $this->securityService->storeLogIndevices($request);
    }

    /**
     * Retrieve a list of currently active devices for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function listActiveDevices(Request $request)
    {
        return $this->securityService->listActiveDevices($request);
    }

    /**
     * Logout a specific device based on the provided device identifier.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logoutSingleDevice(LogoutSingleDeviceRequest $request)
    {
        return $this->securityService->logoutSingleDevice($request->validated());
    }

    /**
     * Logout from all devices associated with the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logoutAllDevices(Request $request)
    {
        return $this->securityService->logoutAllDevices($request);
    }
}

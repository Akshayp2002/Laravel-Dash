<?php

namespace App\Http\Controllers\Api\AccountSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AccountSettings\ChangeProfileRequest;
use App\Http\Requests\Api\AccountSettings\ChangePasswordRequest;
use App\Services\Api\AccountSettings\AccountService;

class AccountController extends Controller
{
    /**
     * Create a new class instance.
     */
    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Update the authenticated user's profile information.
     *
     * @param ChangeProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeProfile(ChangeProfileRequest $request)
    {
        return $this->accountService->changeProfile($request);
    }

    /**
     * Change the authenticated user's password.
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->accountService->changePassword($request->new_password);
    }
}

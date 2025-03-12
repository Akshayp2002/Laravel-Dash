<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Api\Auth\ScreenLockService;
use Illuminate\Http\Request;

class ScreenLockController extends Controller
{
    public function __construct(ScreenLockService $screenLock)
    {
        $this->screenLock = $screenLock;
    }

    public function setupLock(Request $request)
    {
        return $this->screenLock->setupLock($request);
    }
    public function enableLock()
    {
        return $this->screenLock->enableLock();
    }

    public function disableLock()
    {
        return $this->screenLock->disableLock();
    }

    public function verifyLock(Request $request)
    {
        return $this->screenLock->verifyLock($request);
    }

    public function recoveryLock() {}
}

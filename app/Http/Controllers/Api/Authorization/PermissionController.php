<?php

namespace App\Http\Controllers\Api\Authorization;

use App\Http\Controllers\Controller;
use App\Services\Api\Authorization\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }


    public function service() {
        return $this->permissionService->google();
    }
}

<?php

namespace App\Services\Api\Authorization;

use App\Services\BaseService;

class PermissionService extends BaseService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function google() : string {
        return ' I am google.com';
    }
}

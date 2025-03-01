<?php

namespace App\Http\Controllers\Api;

use App\Enum\Permissions\TransactionEnum;
use App\Enum\Permissions\UserEnum;
use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class TestApiController extends Controller
{
    public function index(){

        return User::all();
    }

    public function permissions() {

        // $this->can(UserEnum::UserView);

        // $data = UserEnum::cases();
        // $data = UserEnum::labels();
        // $data = TransactionEnum::labels();
        $data = Permission::fetchAllStaticPermissionKeysWithoutGroup();

        return \response()->json($data);
    }
}

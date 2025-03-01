<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Table\UserTable;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return auth()->user()->profile_photo_url;

        if ($request->ajax()) {
            return UserTable::make();
        }
        return view('developer.user.user');
    }

}

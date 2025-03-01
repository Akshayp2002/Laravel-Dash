<?php

use App\Http\Controllers\Developer\AuditingController;
use App\Http\Controllers\Developer\TestController;
use App\Http\Controllers\Developer\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\DataTables;


Route::get('/', function () {
    return to_route('login');
});

Route::middleware([
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



Route::get('users',[UserController::class,'index'])->name('users');
Route::get('audit',[AuditingController::class,'index'])->name('audit');


Route::get('user-data', function (DataTables $dataTables) {
    $model = User::query();

    return $dataTables->eloquent($model)->toJson();
});

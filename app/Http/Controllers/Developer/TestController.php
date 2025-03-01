<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use App\Table\DemoTable;

class TestController extends Controller
{
    public function index(Request $request){

        $test = TeamInvitation::first();
         $bal =  ['test' =>'arg1' ,'bla'=> $test];
        return DemoTable::make($test);

    }
}

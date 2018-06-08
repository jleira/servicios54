<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use DB;
use Carbon\Carbon;

class UploadfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
   

public function prueba(){
    $users = DB::table('users')
            ->crossJoin('mensajes')
            ->get();
    return response($users);
}

    //
}

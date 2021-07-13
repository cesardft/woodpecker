<?php


namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:retailer');
        $this->middleware('auth:users');
    }

    public function getUser()
    {
        return response()->json(Auth::guard('users')->user());
    }
}

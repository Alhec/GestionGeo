<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static function changePassword(Request $request)
    {
        return UserService::changePassword($request);
    }
}

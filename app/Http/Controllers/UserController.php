<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static function changePassword(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return UserService::changePassword($request,$organizationId);
    }

    public static function changeUserData(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return UserService::changeUserData($request,$organizationId);
    }
}

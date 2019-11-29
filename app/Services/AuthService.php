<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 28/11/19
 * Time: 03:46 PM
 */

namespace App\Services;

use Illuminate\Http\Request;
use App\User;

class AuthService
{
    public static function login(Request $request,$organizationId){
        $credentials= json_decode($request->getContent(),true);
        if (!$token = auth('api')->attempt(['identification'=>$credentials['identification'],
            'password'=>$credentials['password'],
            'user_type'=>$credentials['user_type'],
            'organization_id'=>$organizationId,
            'active'=>1])) {
            return response()->json(['error' => 'Invalid User'], 401);
        }
        return response()->json([
            'token' => $token,
            'type' => 'bearer',
            'expires' => auth('api')->factory()->getTTL() * 60,
            'user' => User::getUserById(auth('api')->user()['id'],$request['user_type'],$organizationId)[0],
        ]);
    }
}

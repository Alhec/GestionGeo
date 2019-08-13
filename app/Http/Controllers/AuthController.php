<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $credentials= json_decode($request->getContent(),true);
        if (!$token = auth('api')->attempt(['identification'=>$credentials['identification'],'password'=>$credentials['password'],'user_type'=>$credentials['user_type']])) {
            return response()->json(['error' => 'Invalid User'], 401);
        }
        return response()->json([
            'token' => $token,
            'type' => 'bearer', // you can ommit this
            'expires' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }

}

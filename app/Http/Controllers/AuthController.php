<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $organizationId = $request->header('organization_key');
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

    public function me()
    {
        return response()->json(auth()->user());
    }
    public function payload()
    {
        return response()->json(auth()->payload());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }

    public function getToken(Request $request)
    {
        $token = auth('api')->getToken();
        return $token;
    }

}

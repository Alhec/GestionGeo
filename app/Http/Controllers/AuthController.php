<?php

namespace App\Http\Controllers;

use App\Log;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    const logLogout = 'Realizo cierre de sesion';
    const taskError = 'No se puede proceder con la tarea';

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }


    public function login(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return AuthService::login($request,$organizationId);
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
        $log = Log::addLog(auth('api')->user()['id'],self::logLogout);
        if (is_numeric($log)&&$log==0){
            return response()->json(['message'=>self::taskError],401);
        }
        auth()->logout();
        return response()->json(['message' => 'Cerro sesión exitosamente']);
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
            'expires_in' => auth()->factory()->getTTL(),
            'user' => auth()->user(),
        ]);
    }

    public function getToken()
    {
        $token = auth('api')->getToken();
        return $token;
    }

}

<?php

namespace App\Http\Middleware;

use Closure;

class RoleAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,...$roles)
    {
        $organizationId = $request->header('Organization-Key');
        if (auth()->payload()['user']->organization_id!=$organizationId){
            return response()->json(['error' => 'Unauthorized'],401);
        }
        $usersRol = array_column(auth()->payload()['user']->roles,'user_type');
        $authorized = false;
        foreach ($usersRol as $rol){
            if(in_array($rol,$roles)){
                $authorized = true;
                break;
            }
        }
        if(!$authorized){
            return response()->json(['error' => 'Unauthorized'],401);
        }
        return $next($request);
    }
}

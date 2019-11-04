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
        if(!in_array(auth()->payload()['user']->user_type,$roles)){
            return response()->json(['error' => 'Unauthorized'],401);
        }
        $organizationId = $request->header('organization_key');
        if (auth()->payload()['user']->organization_id!=$organizationId){
            return response()->json(['error' => 'Unauthorized'],401);
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $token = $request->cookie('jwt_token');
        
        if ($token) {
            
            $user = JWTAuth::setToken($token)->toUser();

            if ($user && $user->role === $role) {
                
                return $next($request);
            }

            return response('Unauthorized', 401);

        }
        return response('Unauthorized', 401);
    }
}

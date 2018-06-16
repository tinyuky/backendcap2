<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class JWTwithAdmin extends \Tymon\JWTAuth\Http\Middleware\BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @throws App\Exceptions\Handler
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->authenticate($request);
        if (!$this->checkaccount($request)) {
            auth()->logout();
            return response()->json(['error' => 'Token is rejected', 'action' => 'login'], 400);
        }
        return $next($request);
    }
    public function checkaccount($request)
    {
        $user = JWTAuth::parseToken()->authenticate($request);
        if (($user->role === 'admin') && ($user->status == true)) {
            return true;
        } else {
            return false;
        }
    }
}

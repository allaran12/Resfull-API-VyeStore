<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
    {
        $token = $request->header('TTOKEN') ?: $request->input('ttoken');

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();
            $request->attributes->set('auth_user', $user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return $next($request);
    }
}

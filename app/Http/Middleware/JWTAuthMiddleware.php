<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // JWT token doğrulaması
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return Response::json(['error' => 'User not authenticated'], 401);
            }
        } catch (JWTException $e) {
            return Response::json(['error' => 'Token is invalid'], 401);
        }

        // Kullanıcıyı request'e ekliyoruz
        $request->user = $user;

        return $next($request);
    }
}

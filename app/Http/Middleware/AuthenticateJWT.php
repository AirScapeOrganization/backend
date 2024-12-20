<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class AuthenticateJWT
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        try {

            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            if (!isset($decoded->sub)) {
                return response()->json(['message' => 'Token does not contain user ID'], 401);
            }

            if (!isset($decoded->is_owner)) {
                return response()->json(['message' => 'Token does not contain property "is_owner"'], 401);
            }

            $request->user = $decoded;
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}

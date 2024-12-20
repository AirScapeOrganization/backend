<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTokenOwner
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();

            $decodedToken = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            $owner = $decodedToken->is_owner ?? null;

            if ($owner == null || $owner == 0) {
                return response()->json([
                    'message' => 'Invalid owner value',
                    'status_code' => 403
                ], 403);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Invalid token',
                'status_code' => 401
            ], 401);
        }

        return $next($request);
    }
}

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
    $authorizationHeader = $request->header('Authorization');

    if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
        return response()->json(['message' => 'Encabezado Authorization no válido o ausente'], 401);
    }

    $token = $request->bearerToken();

    if (!$token) {
        return response()->json(['message' => 'Token no proporcionado'], 401);
    }

    try {
        $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        $request->user = $decoded; // Guarda el usuario en la solicitud
    } catch (\Exception $e) {
        return response()->json(['message' => 'Token inválido'], 401);
    }

    return $next($request);
}

}


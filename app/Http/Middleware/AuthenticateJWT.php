<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class AuthenticateJWT
{
    public function handle(Request $request, Closure $next) {

        $token = $request->bearerToken(); // Obtener el token del encabezado Authorization

        if (!$token) {
            return response()->json(['message' => 'Token no proporcionado'], 401);
        }

        try {
            // Decodificar el JWT
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            if (!isset($decoded->sub)) {
                return response()->json(['message' => 'Token no contiene ID de usuario'], 401);
            }


            if (!isset($decoded->is_owner)) {
                return response()->json(['message' => 'Token no contiene la propiedad "is_owner"'], 401);
            }


            $request->user = $decoded; 

        } catch (\Exception $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        return $next($request);
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $payload = [
                'iss' => "airscape_user",
                'sub' => $user->user_id,
                'iat' => time(),
                'exp' => time() + 60 * 60,  // 1 hora
                'is_owner' => $user->is_owner
            ];
            

            $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'token' => $jwt,
                'user' => $user,
            ], 200);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }
}


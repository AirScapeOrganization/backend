<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
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
                "role" => "authenticated",
                "ref" => bin2hex(random_bytes(10)), 
                'sub' => $user->user_id,
                'is_owner' => $user->is_owner,
                'iat' => time(),
                'exp' => time() + 60 * 60, 
            ];

            $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');


            $idSession = uniqid('sess_', true);

            Session::put('id_session', $idSession);
            Session::put('user', [
                'user_id' => $user->user_id,
                'role' => $user->role,
                'is_owner' => $user->is_owner,
                'token' => $jwt,
            ]);

            return response()->json([
                'message' => 'Successful login',
                'token' => $jwt,
                'id_session' => $idSession,
            ], 200);
        }

        return response()->json(['message' => 'Incorrect credentials'], 401);
    }
}

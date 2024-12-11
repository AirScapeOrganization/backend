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
                "role" => "authenticated",
                "ref" => "owginxrurpipnipdwiwr",
                'sub' => $user->user_id,
                'is_owner' => $user->is_owner,
                'iat' => time(),
                'exp' => time() + 60 * 60,
            ];
            

            $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            return response()->json([
                'message' => 'Successful login',
                'token' => $jwt,
            ], 200);
        }

        return response()->json(['message' => 'Incorrect credentials'], 401);
    }
}


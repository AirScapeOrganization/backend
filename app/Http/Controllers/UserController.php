<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::all();

        return response()->json(["Users" => $user]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'profile_picture' => 'required',
            'bio' => 'required',
            'is_owner' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $validator->errors(),
                'status' => 404
            ], 404);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $request->profile_picture,
            'bio' => $request->bio,
            'is_owner' => $request->is_owner
        ]);

        if (!$user) {
            return response()->json([
                'mensaje' => 'Error al crear usuario',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'user' => $user,
            'status' => 201
        ], 201);
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $validator->errors(),
                'status' => 400
            ], 400);
        }


        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'mensaje' => 'No existe este usuario',
                'status' => 404
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'mensaje' => 'Credenciales incorrectas',
                'status' => 401
            ], 401);
        }

        $payload = [
            'iss' => "user_airscape",
            'sub' => $user->user_id,
            'username' => $user->username,
            'email' => $user->email,
            'exp' => time() + 3600
        ];

        $secretKey = env('JWT_SECRET');

        try {
            $token = JWT::encode($payload, $secretKey, 'HS256');
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'No se pudo generar el token',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }

        return response()->json([
            'mensaje' => 'Inicio de sesión exitoso',
            'token' => $token,
            'status' => 200
        ], 200);
    }
}

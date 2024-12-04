<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::all();
        $data = [
            'users' => $usuarios,
            'status' => 200,
        ];

        return response()->json($data, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'profile_picture' => 'required',
            'bio' => 'required',
            'is_owner' => 'required|boolean'

        ]);

        if ($validacion->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validacion de datos',
                'error' => $validacion->errors(),
                'status' => 400
            ], 400);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $request->profile_picture,
            'bio' => $request->bio,
            'is_owner' => $request->is_owner
        ]);

        $payload = [
            'iss' => "airscape_user",
            'sub' => $user->user_id,
            'iat' => time(),
            'exp' => time() + 60 * 60,  // 1 hora
            'is_owner' => $user->is_owner
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'mensaje' => 'Usuario creado con exito',
            'user' => $user,
            'token' => $jwt,
            'status' => 200
        ], 200);
    }

    public function showUser($user_id){
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'user_id' => $user->user_id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->is_owner ? 'Owner' : 'Tenant',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function checkUsername($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            $data = [
                'mensaje' => 'Usuario not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'user' => $user,
            'status' => 200,
            'message' => 'User exists'
        ];

        return response()->json($data, 200);
    }

    public function checkEmail($email)
    {
        $email = User::where('email', $email)->first();

        if (!$email) {
            $data = [
                'mensaje' => 'Email not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'user' => $email,
            'status' => 200,
            'message' => 'User exists'
        ];

        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {

        $validacion = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'profile_picture' => 'required',
            'bio' => 'required',
            'is_owner' => 'required|boolean'
        ]);

        if ($validacion->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $validacion->errors(),
                'status' => 400
            ]);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => 404
            ]);
        }

        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->profile_picture = $request->profile_picture;
        $user->bio = $request->bio;
        $user->is_owner = $request->is_owner;

        if (!$user->save()) {
            return response()->json([
                'mensaje' => 'No se pudo actualizar el usuario',
                'status' => 500
            ]);
        }

        return response()->json([
            'mensaje' => 'Usuario actualizado correctamente',
            'status' => 200
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => 404
            ]);
        }

        if (!$user->delete()) {
            return response()->json([
                'mensaje' => 'No se pudo eliminar el usuario',
                'status' => 500
            ]);
        }

        return response()->json([
            'mensaje' => 'Usuario eliminado correctamente',
            'status' => 200
        ]);
    }
}

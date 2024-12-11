<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        $allUsers = User::all();
        $data = [
            'users' => $allUsers,
            'status' => 200,
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'profile_picture' => 'required',
            'bio' => 'required',
            'is_owner' => 'required|boolean'

        ]);

        if ($validated->fails()) {
            return response()->json([
                'mensaje' => 'Data validation error',
                'error' => $validated->errors(),
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
            'exp' => time() + 60 * 60,  // 1 hour
            'is_owner' => $user->is_owner
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'mensaje' => 'User created successfullyy',
            'token' => $jwt,
            'status' => 200
        ], 200);
    }

    public function show($id){
        $user = User::find($id);

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
 

    public function edit(Request $request, $id)
    {

        $validated = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'profile_picture' => 'required',
            'bio' => 'required',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'mensaje' => 'Data validation error',
                'error' => $validated->errors(),
                'status' => 400
            ]);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'mensaje' => 'User not found',
                'status' => 404
            ]);
        }

        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->profile_picture = $request->profile_picture;
        $user->bio = $request->bio;

        if (!$user->save()) {
            return response()->json([
                'mensaje' => 'Failed to update user',
                'status' => 500
            ]);
        }

        return response()->json([
            'mensaje' => 'User successfully updated',
            'status' => 200
        ]);
    }

    public function destroy(string $id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'mensaje' => 'User not found',
                'status' => 404
            ]);
        }

        if (!$user->delete()) {
            return response()->json([
                'mensaje' => 'Could not delete user',
                'status' => 500
            ]);
        }

        return response()->json([
            'mensaje' => 'User deleted successfully',
            'status' => 200
        ]);
    }
}

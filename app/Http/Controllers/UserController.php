<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $allUsers = User::all();
        return response()->json([
            'users' => $allUsers,
            'status' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'profile_picture' => 'nullable|string',
            'bio' => 'nullable|string',
            'is_owner' => 'nullable|boolean',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_picture' => $validated['profile_picture'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'is_owner' => $validated['is_owner'] ?? false,
        ]);

        $payload = [
            'iss' => "airscape_user",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 60 * 60, // 1 hour
            'is_owner' => $user->is_owner,
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'mensaje' => 'User created successfully',
            'token' => $jwt,
            'status' => 200,
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        return response()->json([
            'user_id' => $user->user_id,
            'username' => $user->username,
            'email' => $user->email,
            'bio' => $user->bio,
            'profile_picture' => $user->profile_picture,
            'role' => $user->is_owner ? 'Owner' : 'Tenant',
        ]);
    }

    public function edit(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'mensaje' => 'User not found',
                'status' => 404,
            ]);
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:6',
            'profile_picture' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);

        $user->username = $validated['username'];
        $user->email = $validated['email'];
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->profile_picture = $validated['profile_picture'] ?? $user->profile_picture;
        $user->bio = $validated['bio'] ?? $user->bio;

        if (!$user->save()) {
            return response()->json([
                'mensaje' => 'Failed to update user',
                'status' => 500,
            ]);
        }

        return response()->json([
            'mensaje' => 'User successfully updated',
            'status' => 200,
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'mensaje' => 'User not found',
                'status' => 404,
            ]);
        }

        if (!$user->delete()) {
            return response()->json([
                'mensaje' => 'Could not delete user',
                'status' => 500,
            ]);
        }

        return response()->json([
            'mensaje' => 'User deleted successfully',
            'status' => 200,
        ]);
    }
}

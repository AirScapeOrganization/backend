<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\Key;

class PhotosController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'photo' => 'required|file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Data validation error',
                'errors' => $validated->errors(),
                'status' => 400
            ], 400);
        }

        $token = $request->header('Authorization');

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        $userId = $decoded->sub;

        $photo = $request->file('photo');

        if ($photo) {
            try {
                $uploadedUrl = $this->supabase->uploadImage($photo, $userId);
                if (!$uploadedUrl) {
                    throw new \Exception("Error uploading the image");
                }

                return response()->json([
                    'message' => 'Photo uploaded successfully',
                    'uploaded_url' => $uploadedUrl,
                    'status' => 201
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 500
                ], 500);
            }
        }

        return response()->json([
            'message' => 'No photo uploaded',
            'status' => 400
        ], 400);
    }
}

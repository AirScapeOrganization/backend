<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            'listing_id' => 'required|integer',
            'photo' => 'required|file|mimes:jpg,jpeg,png,webp',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Data validation error',
                'errors' => $validated->errors(),
                'status' => 400
            ], 400);
        }

        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 401
            ], 401);
        }

        $listingId = $request->input('listing_id');
        $photo = $request->file('photo');

        try {
            $uploadedUrl = $this->supabase->uploadSingleImage($photo, $userId, $listingId);

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
}

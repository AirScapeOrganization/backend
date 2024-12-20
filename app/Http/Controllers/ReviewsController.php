<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{

    public function index()
    {
        $allReviews = Reviews::all();
        $data = [
            'reviews' => $allReviews,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $userInfo = $request->user();
        $user_id = $userInfo->user_id;

        $validated = Validator::make($request->all(), [
            'listing_id' => 'required|integer|exists:listings,listing_id',
            'rating' => 'required|integer',
            'comment' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Data validation error',
                'errors' => $validated->errors(),
                'status' => 400
            ], 400);
        }

        $createReview = Reviews::create([
            'listing_id' => $request->listing_id,
            'user_id' => $user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review created successfully',
            'review' => $createReview,
        ], 201);
    }

    public function show($id)
    {
        $review_id = Reviews::find($id);

        if (!$review_id) {
            return response()->json([
                'mensaje' => 'Review not found',
                'status' => 404,
            ], 404);
        }
        return response()->json([
            'review' => $review_id,
            'status' => 200,
        ], 200);
    }

    public function update(Request $request, $review_id)
    {
        $validated = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'mensaje' => 'Data validation error',
                'error' => $validated->errors(),
                'status' => 400
            ]);
        }

        $updatedReview = Reviews::where('review_id', $review_id)
            ->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

        if (!$updatedReview) {
            return response()->json([
                'mensaje' => 'Failed to update review',
                'status' => 500,
            ]);
        }

        return response()->json([
            'mensaje' => 'Review updated successfully',
            'status' => 200
        ]);
    }

    public function destroy($review_id)
    {
        $existingReview = Reviews::find($review_id);

        if (!$existingReview) {
            return response()->json([
                'mensaje' => 'Review not found',
                'status' => 404
            ]);
        }

        if ($existingReview->delete()) {
            return response()->json([
                'mensaje' => 'Review deleted successfully',
                'status' => 200
            ]);
        }
        return response()->json([
            'mensaje' => 'Could not delete review',
            'status' => 500
        ]);
    }
}

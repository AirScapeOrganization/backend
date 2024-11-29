<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Reviews = Reviews::all();
    
        $data = [
            'Reviews' => $Reviews,
            'status' => 200,
        ];
    
        return response()->json($data, 200);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(Request $request, $review_id)
{
    $reviewsValidator = Validator::make($request->all(), [
        'listing_id' => 'required|exists:listings,listing_id', 
        'rating' => 'required|integer|min:1|max:5', 
        'comment' => 'nullable|string', 
    ]);

    if ($reviewsValidator->fails()) {
        return response()->json([
            'mensaje' => 'Error en la validación de datos',
            'error' => $reviewsValidator->errors(),
            'status' => 400
        ]);
    }

    $existingReview = Reviews::find($review_id);

    if (!$existingReview) {
        return response()->json([
            'mensaje' => 'Reseña no encontrada',
            'status' => 404
        ]);
    }
    $existingReview->listing_id = $request->listing_id;
    $existingReview->rating = $request->rating;
    $existingReview->comment = $request->comment;
    $existingReview->created_at = now(); 
    
    if (!$existingReview->save()) {
        return response()->json([
            'mensaje' => 'No se pudo actualizar la reseña',
            'status' => 500
        ]);
    }
    return response()->json([
        'mensaje' => 'Reseña actualizada correctamente',
        'review' => $existingReview,
        'status' => 200
    ]);
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $Reviews = Reviews::find($id);
   
        if (!$Reviews) {
            return response()->json([
                'mensaje' => 'Review no se pudo encontrar',
                'status' => 404,
            ], 404);
        }
        return response()->json([
            'review' => $Reviews,
            'status' => 200,
        ], 200);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($review_id)
{
    $existingReview = Reviews::find($review_id);

    if (!$existingReview) {
        return response()->json([
            'mensaje' => 'Reseña no encontrada',
            'status' => 404
        ]);
    }

    if ($existingReview->delete()) {
        return response()->json([
            'mensaje' => 'Reseña eliminada correctamente',
            'status' => 200
        ]);
    }
    return response()->json([
        'mensaje' => 'No se pudo eliminar la reseña',
        'status' => 500
    ]);
}

}

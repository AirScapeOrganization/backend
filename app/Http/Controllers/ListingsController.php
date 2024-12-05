<?php

namespace App\Http\Controllers;

use App\Models\Listings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ListingsController extends Controller
{
    public function index()
    {
        $listings = Listings::all();

        $data = [
            'properties' => $listings,
            'status' => 200,
        ];

        if ($listings->isEmpty()) {
            return response()->json(['message' => 'No listings found'], 404);
        }

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {

        $user = $request->user;

        if ($user->is_owner !== 1) {
            return response()->json([
                'message' => 'No tienes permisos para crear una propiedad'
            ], 403);
        }

        $listings = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price_per_night' => 'required|numeric',
            'num_bedrooms' => 'required|integer',
            'num_bathrooms' => 'required|integer',
            'max_guests' => 'required|integer',
            'photo_url' => 'required|file',
        ]);

        if ($listings->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $listings->errors(),
                'status' => 400
            ]);
        }

        try {
            // Subir la imagen a Supabase (si se ha enviado una)
            $uploadedUrl = null;
            if ($request->hasFile('photo_url')) {
                $uploadedUrl = $this->supabase->uploadImage($request->file('photo_url'));
                
                if (!$uploadedUrl) {
                    return response()->json([
                        'message' => 'Error al subir la imagen a Supabase',
                        'status' => 500
                    ], 500);
                }
            }
    

            $listing = Listings::create([
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'price_per_night' => $request->price_per_night,
                'num_bedrooms' => $request->num_bedrooms,
                'num_bathrooms' => $request->num_bathrooms,
                'max_guests' => $request->max_guests,
                'user_id' => $user->sub,
                'photo_url' => $uploadedUrl
            ]);
    
            return response()->json([
                'message' => 'Propiedad creada exitosamente',
                'listing' => $listing
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el listing',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function edit(Request $request, string $id)
    {
        $listings = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price_per_night' => 'required|numeric',
            'num_bedrooms' => 'required|integer',
            'num_bathrooms' => 'required|integer',
            'max_guests' => 'required|integer',
        ]);

        if ($listings->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $listings->errors(),
                'status' => 400
            ]);
        }
        $listings = Listings::find($id);

        if (!$listings) {
            return response()->json([
                'mensaje' => 'Propiedad no encontrada',
                'status' => 404
            ]);
        }
        $listings->title = $request->title;
        $listings->description = $request->description;
        $listings->address = $request->address;
        $listings->latitude = $request->latitude;
        $listings->longitude = $request->longitude;
        $listings->price_per_night = $request->price_per_night;
        $listings->num_bedrooms = $request->num_bedrooms;
        $listings->num_bathrooms = $request->num_bathrooms;
        $listings->max_guests = $request->max_guests;

        if (!$listings->save()) {
            return response()->json([
                'mensaje' => 'No se pudo actualizar la propiedad',
                'status' => 500
            ]);
        }

        return response()->json([
            'mensaje' => 'Propiedad actualizada correctamente',
            'status' => 200
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Listings;
use App\Models\Photos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseService;  

class ListingsController extends Controller
{
    public $supabase;

    // Inyectamos el servicio de Supabase en el controlador
    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabase = $supabaseService;
    }

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

    public function create()
    {
        //
    }

    public function store(Request $request)
{
    // Validación de los datos de entrada
    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'description' => 'required|string',
        'address' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'price_per_night' => 'required|numeric',
        'num_bedrooms' => 'required|integer',
        'num_bathrooms' => 'required|integer',
        'max_guests' => 'required|integer',
        'photo_url' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'mensaje' => 'Error en la validación de datos',
            'error' => $validator->errors(),
            'status' => 400,
        ], 400);
    }

    // Subir la imagen a Supabase
    try {
        $uploadedUrl = $this->supabase->uploadImage($request->file('photo_url'));

        if (!$uploadedUrl) {
            return response()->json([
                'mensaje' => 'Error al subir la imagen a Supabase',
                'status' => 500
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'mensaje' => 'Error durante la subida de la imagen: ' . $e->getMessage(),
            'status' => 500
        ], 500);
    }

    try {
        // Crear el nuevo listing
        $newListing = new Listings();
        $newListing->title = $request->title;
        $newListing->description = $request->description;
        $newListing->address = $request->address;
        $newListing->latitude = $request->latitude;
        $newListing->longitude = $request->longitude;
        $newListing->price_per_night = $request->price_per_night;
        $newListing->num_bedrooms = $request->num_bedrooms;
        $newListing->num_bathrooms = $request->num_bathrooms;
        $newListing->max_guests = $request->max_guests;
        $newListing->user_id = $request->user_id ?? 1;  // Valor por defecto si no se pasa user_id
        $newListing->created_at = now();

        $newListing->save();

        // Crear foto para el nuevo listing, asociando la URL de la imagen subida
        $photo = new Photos();
        $photo->listing_id = $newListing->listing_id;  
        $photo->photo_url = $uploadedUrl; // URL de la imagen de Supabase
        $photo->created_at = now();

        $photo->save();

        return response()->json([
            'mensaje' => 'Propiedad creada correctamente',
            'listing' => $newListing,
            'photo' => $photo,
            'status' => 200
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'mensaje' => 'Hubo un error al crear la propiedad o al guardar la foto: ' . $e->getMessage(),
            'status' => 500
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
   
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
        //
    }
}

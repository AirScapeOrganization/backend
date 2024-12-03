<?php

namespace App\Http\Controllers;

use App\Models\Listings;
use App\Models\Photos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ListingsController extends Controller
{
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
            'photo_url' => 'required|string' // URL de la foto
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $validator->errors(),
                'status' => 400
            ]);
        }

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
        $newListing->user_id = $request->user_id ?? 1;  // valor por defecto si no se pasa user_id

        if (!$newListing->save()) {
            return response()->json([
                'mensaje' => 'No se pudo crear la propiedad',
                'status' => 500
            ]);
        }

        // Guardar la foto en la tabla Photos
        $photo = new Photos();
        $photo->listing_id = $newListing->listing_id;
        $photo->photo_url = $request->photo_url; // Asumiendo que la URL de la foto se envía como parte de la solicitud

        if (!$photo->save()) {
            return response()->json([
                'mensaje' => 'No se pudo guardar la foto',
                'status' => 500
            ]);
        }

        // Retornar respuesta exitosa
        return response()->json([
            'mensaje' => 'Propiedad y foto creada correctamente',
            'listing' => $newListing,
            'photo' => $photo,
            'status' => 201
        ]);
    }
}

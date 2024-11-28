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

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $user = $request->user;

        if ($user->is_owner !== 1) {
            return response()->json([
                'message' => 'No tienes permisos para crear una propiedad'
            ], 403);
        }

        // Validar los datos de entrada
        $validacion = Validator::make($request->all(), [
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

        if ($validacion->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'errores' => $validacion->errors(),
                'status' => 400
            ], 400);
        }

        // Crear el nuevo listing
        try {
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



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

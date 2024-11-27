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

        return response()->json($data, 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $user = $request->user; // Usuario decodificado del token

        if (!$user->is_owner) {
            return response()->json([
                'message' => 'No tienes permisos para crear una propiedad',
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
            $listings = Listings::create(array_merge(
                $request->only([
                    'title',
                    'description',
                    'address',
                    'latitude',
                    'longitude',
                    'price_per_night',
                    'num_bedrooms',
                    'num_bathrooms',
                    'max_guests'
                ]),
                ['user_id' => $user->id]
            ));

            return response()->json([
                'mensaje' => 'Listing creado correctamente',
                'data' => $listings,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el listing',
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

<?php

namespace App\Http\Controllers;

use App\Models\Listings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listings = Listings::all();
        $data = [
            'properties'=>$listings,
            'status'=> 200,
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
public function store(Request $request)
{
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
        $listings = Listings::create($request->only([
             'title', 'description', 'address', 'latitude',
            'longitude', 'price_per_night', 'num_bedrooms', 'num_bathrooms', 'max_guests'
        ]));

        return response()->json([
            'mensaje' => 'Listing creado correctamente',
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

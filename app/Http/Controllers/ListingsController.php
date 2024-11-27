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

    // Si la validación falla
    if ($validacion->fails()) {
        return response()->json([
            'mensaje' => 'Error en la validación de datos',
            'errores' => $validacion->errors(),
            'status' => 400
        ], 400);
    }

    // Crear la propiedad
    $listings = Listings::create([
        'title' => $request->title,
        'description' => $request->description,
        'address' => $request->address,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'price_per_night' => $request->price_per_night,
        'num_bedrooms' => $request->num_bedrooms,
        'num_bathrooms' => $request->num_bathrooms,
        'max_guests' => $request->max_guests,
    ]);

    // Verificar si la creación y guardado fueron exitosos
    if (!$listings) {
        return response()->json([
            'mensaje' => 'Error al crear la propiedad',
            'status' => 500
        ], 500);
    }

    return response()->json([
        'mensaje' => 'Propiedad creada correctamente',
        'status' => 200
    ], 200);
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

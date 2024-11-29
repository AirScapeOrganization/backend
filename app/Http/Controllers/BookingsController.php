<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BookingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Bookings = Bookings::all();
    
        $data = [
            'Bookings' => $Bookings,
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after:tomorrow',
            'end_date' => 'required|date|after:start_date',
            'total_price' => 'required|numeric',
            'listing_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $validator->errors(),
                'status' => 400
            ]);
        }
    
        
        $newBooking = new Bookings();
        $newBooking->start_date = $request->start_date;
        $newBooking->end_date = $request->end_date;
        $newBooking->total_price = $request->total_price;
        $newBooking->listing_id = $request->listing_id;
        $newBooking->user_id = $request->user_id;
        $newBooking->created_at = now();
    

        if (!$newBooking->save()) {
            return response()->json([
                'mensaje' => 'No se pudo crear la reserva',
                'status' => 500
            ]);
        }
    
        return response()->json([
            'mensaje' => 'Reserva creada correctamente',
            'booking' => $newBooking,
            'status' => 201
        ]);
    }
    


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Bookings = Bookings::find($id);
   
        if (!$Bookings) {
            return response()->json([
                'mensaje' => 'Reserva no se pudo encontrar',
                'status' => 404,
            ], 404);
        }
        return response()->json([
            'Reserva' => $Bookings,
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

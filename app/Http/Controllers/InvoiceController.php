<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$Invoice = Invoices::all();
    
        //$data = [
          //  'Invoice' => $Invoice,
            //'status' => 200,
        //];
        //return response()->json($data, 200);
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
        $Invoice = Validator::make($request->all(), [
            'date' => 'required|date', 
            'time' => 'required|date_format:H:i',  
            'tax_price' => 'required|numeric',  
            'price_gross' => 'required|numeric',  
            'price_net' => 'required|numeric',  
        ]);
        

        if ($Invoice->fails()){
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $Invoice->errors(),
                'status' => 400
            ]);
        }
        $newInvoice = new Invoices();
        $newInvoice->date = $request->date;
        $newInvoice->time = $request->time;
        $newInvoice->tax_price = $request->tax_price;
        $newInvoice->price_gross = $request->price_gross;       
        $newInvoice->price_net = $request->price_net;
        $newInvoice->booking_id = $request->booking_id ?? 1;

        if (!$newInvoice->save()) {
            return response()->json([
                'mensaje' => 'No se pudo crear la factura',
                'status' => 500
            ]);
        }
        return response()->json([
            'mensaje' => 'Factura creada correctamente',
            //'invoice' => $newInvoice,
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Invoice = Invoices::find($id);
   
        if (!$Invoice) {
            return response()->json([
                'message' => 'Invoice not found',
                'status' => 404,
            ], 404);
        }
        return response()->json([
            'Factura' => $Invoice,
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

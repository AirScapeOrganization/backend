<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{

    public function store(Request $request)
    {
        $Invoice = Validator::make($request->all(), [
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'tax_price' => 'required|numeric',
            'price_gross' => 'required|numeric',
            'price_net' => 'required|numeric',
        ]);

        if ($Invoice->fails()) {
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
            'status' => 201
        ]);
    }


    public function show(Request $request, $id)
    {
        $user = $request->user();

        $invoicesUser = Invoices::where('user_id', $user->user_id)->get();

        $data = [
            'invoices' => $invoicesUser,
            'status' => 200,
        ];

        return response()->json($data, 200);
    }

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}


    public function destroy(string $id) {}
}

<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BookingsController extends Controller
{
    public function store(Request $request)
    {
        $userId = $request->user()->user_id;

        $validator = Validator::make($request->all(), [
            'listing_id' => 'required|exists:listings,listing_id',
            'start_date' => 'required|date|after:tomorrow',
            'end_date' => 'required|date|after:start_date',
            'total_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Data validation error',
                'error' => $validator->errors(),
                'status' => 400
            ]);
        }


        $newBooking = new Bookings();
        $newBooking->user_id = $userId;
        $newBooking->listing_id = $request->listing_id;
        $newBooking->start_date = $request->start_date;
        $newBooking->end_date = $request->end_date;
        $newBooking->total_price = $request->total_price;


        if (!$newBooking->save()) {
            return response()->json([
                'mensaje' => 'Reservation could not be created',
                'status' => 500
            ]);
        }
    
        return response()->json([
            'mensaje' => 'Reservation created successfully',
            'booking' => $newBooking,
            'status' => 201
        ]);
    }

    public function show(Request $request)
    {
        $userId = $request->user()->user_id;

        $bookings = Bookings::with('listing')
        ->where('user_id', $userId)
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found for this user'], 404);
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'booking_id' => $booking->booking_id,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
                'total_price' => $booking->total_price,
                'listing' => [
                    'id' => $booking->listing->listing_id,
                    'title' => $booking->listing->title,
                    'address' => $booking->listing->address,
                    'price_per_night' => $booking->listing->price_per_night,
                ],
            ];
        });

        return response()->json(['bookings' => $formattedBookings, 'status' => 200], 200);
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

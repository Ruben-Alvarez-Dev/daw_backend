<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::all();
        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'table_ids' => 'nullable|array',
            'pax_number' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:pending,confirmed,seated,cancelled',
        ]);

        $reservation = Reservation::create($validatedData);

        if ($reservation) {
            return response()->json($reservation, 201);
        } else {
            return response()->json(['error' => 'Not possible to create reservation'], 500);
        }
    }

    public function show(Reservation $reservation)
    {
        return response()->json($reservation);
    }

    public function update(Request $request, Reservation $reservation)
    {

        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'table_ids' => 'nullable|array',
            'pax_number' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:pending,confirmed,seated,cancelled',
        ]);

        $reservation->update($validatedData);

        return response()->json($reservation);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return response()->json(null, 204);
    }
}

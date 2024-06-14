<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the reservations.
     */
    public function index()
    {
        $reservations = Reservation::all();
        return response()->json($reservations);
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $reservation = Reservation::create($request->all());
        return response()->json($reservation, 201);
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation)
    {
        return response()->json($reservation);
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update($request->all());
        return response()->json($reservation);
    }

    /**
     * Remove the specified reservation from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return response()->json(null, 204);
    }
}
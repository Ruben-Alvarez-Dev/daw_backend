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
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'table_ids' => 'nullable|array', // Cambiado a 'table_ids'
            'pax_number' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        // Crear la reserva
        $reservation = Reservation::create($validatedData);

        // Verificar si la reserva se creó exitosamente
        if ($reservation) {
            return response()->json($reservation, 201);
        } else {
            return response()->json(['error' => 'No se pudo crear la reserva'], 500);
        }
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
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'table_ids' => 'nullable|array', // Cambiado a 'table_ids'
            'pax_number' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        // Actualizar la reserva
        $reservation->update($validatedData);

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

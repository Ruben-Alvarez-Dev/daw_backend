<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = auth()->user();
        $reservations = $user->isAdmin() 
            ? Reservation::with(['user', 'table'])->get()
            : Reservation::where('user_id', $user->id)->with(['user', 'table'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $reservations
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i:s',
            'guests' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Verificar si la mesa está disponible
        $table = Table::findOrFail($request->table_id);
        if (!$table->isAvailable()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Table is not available'
            ], 422);
        }

        // Verificar si ya existe una reserva para esta mesa en la misma fecha y hora
        $existingReservation = Reservation::where('table_id', $request->table_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('status', 'confirmed')
            ->first();

        if ($existingReservation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Table is already reserved for this date and time'
            ], 422);
        }

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'table_id' => $request->table_id,
            'date' => $request->date,
            'time' => $request->time,
            'guests' => $request->guests,
            'notes' => $request->notes,
            'status' => 'confirmed'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation created successfully',
            'data' => $reservation
        ], 201);
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        return response()->json([
            'status' => 'success',
            'data' => $reservation->load(['user', 'table'])
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $request->validate([
            'guests' => 'sometimes|integer|min:1',
            'notes' => 'sometimes|nullable|string'
        ]);

        $reservation->update($request->only(['guests', 'notes']));

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation updated successfully',
            'data' => $reservation
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation cancelled successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
{
    $reservations = Reservation::all();

    // Opcional: carga las mesas relacionadas para cada reserva
    foreach ($reservations as $reservation) {
        $tables = [];
        if (is_array($reservation->table_ids)) {
            $tables = Table::whereIn('table_id', $reservation->table_ids)->get();
        } elseif (is_string($reservation->table_ids)) {
            $tableIds = json_decode($reservation->table_ids, true);
            if (is_array($tableIds)) {
                $tables = Table::whereIn('table_id', $tableIds)->get();
            }
        }
        $reservation->setAttribute('tables', $tables);
    }

    return response()->json($reservations);
}
    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);

        // Opcional: carga las mesas relacionadas para la reserva
        $tables = [];
        if (!is_null($reservation->table_ids) && $reservation->table_ids !== '') {
            $tableIds = json_decode($reservation->table_ids, true);
            if (is_array($tableIds)) {
                $tables = Table::whereIn('table_id', $tableIds)->get();
            }
        }
        $reservation->setAttribute('tables', $tables);

        return response()->json($reservation);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'table_ids' => 'nullable|array',
            'table_ids.*' => 'nullable|exists:tables,table_id',
            'pax_number' => 'required|integer|min:1',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        if (!empty($validatedData['table_ids'])) {
            $validatedData['table_ids'] = serialize($validatedData['table_ids']);
        } else {
            $validatedData['table_ids'] = null;
        }

        $reservation = Reservation::create($validatedData);

        return response()->json($reservation, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'table_ids' => 'sometimes|array',
            'table_ids.*' => 'sometimes|exists:tables,table_id',
            'pax_number' => 'sometimes|integer|min:1',
            'date' => 'sometimes|date',
            'time' => 'sometimes|date_format:H:i',
            'status' => 'sometimes|in:pending,confirmed,canceled',
        ]);
    
        $reservation = Reservation::findOrFail($id);
    
        if ($request->has('table_ids')) {
            $validatedData['table_ids'] = $validatedData['table_ids'];
        }
    
        $reservation->update($validatedData);
    
        // Cargar las mesas relacionadas para la respuesta (opcional)
        $tables = [];
        if (!empty($reservation->table_ids)) {
            $tables = Table::whereIn('table_id', $reservation->table_ids)->get();
        }
        $reservation->setAttribute('tables', $tables);
    
        return response()->json($reservation);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(['message' => 'Reservación eliminada correctamente']);
    }
}
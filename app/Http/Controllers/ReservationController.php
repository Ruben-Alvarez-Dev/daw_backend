<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('user', 'tables')->get();
        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'table_ids' => 'required|array',
            'table_ids.*' => 'required|exists:tables,table_id',
            'pax_number' => 'required|integer|min:1',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $tables = Table::whereIn('table_id', $validatedData['table_ids'])->get();
        $totalCapacity = $tables->sum('capacity');

        if ($totalCapacity < $validatedData['pax_number']) {
            return response()->json(['error' => 'La capacidad total de las mesas seleccionadas es insuficiente'], 400);
        }

        $reservation = Reservation::create($validatedData);
        $reservation->tables()->attach($validatedData['table_ids']);

        return response()->json($reservation->load('user', 'tables'), 201);
    }

    public function show($id)
    {
        $reservation = Reservation::with('user', 'tables')->findOrFail($id);
        return response()->json($reservation);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'table_ids' => 'required|array',
            'table_ids.*' => 'required|exists:tables,table_id',
            'pax_number' => 'required|integer|min:1',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $tables = Table::whereIn('table_id', $validatedData['table_ids'])->get();
        $totalCapacity = $tables->sum('capacity');

        if ($totalCapacity < $validatedData['pax_number']) {
            return response()->json(['error' => 'La capacidad total de las mesas seleccionadas es insuficiente'], 400);
        }

        $reservation = Reservation::findOrFail($id);
        $reservation->update($validatedData);
        $reservation->tables()->sync($validatedData['table_ids']);

        return response()->json($reservation->load('user', 'tables'));
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->tables()->detach();
        $reservation->delete();

        return response()->json(['message' => 'Reservación eliminada correctamente']);
    }
}
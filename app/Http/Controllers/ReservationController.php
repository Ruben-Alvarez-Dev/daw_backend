<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $reservations = Reservation::with(['user', 'table', 'creator'])->get();
        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'datetime' => 'required|date',
            'guests' => 'required|integer|min:1',
            'table_id' => 'required|exists:tables,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $reservation = Reservation::create([
            'datetime' => $request->datetime,
            'guests' => $request->guests,
            'table_id' => $request->table_id,
            'user_id' => $request->user_id,
            'created_by' => auth()->id()
        ]);

        return response()->json($reservation->load(['user', 'table', 'creator']), 201);
    }

    public function show(Reservation $reservation)
    {
        return response()->json($reservation->load(['user', 'table', 'creator']));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'datetime' => 'date',
            'guests' => 'integer|min:1',
            'status' => 'in:pending,confirmed,seated,cancelled,no-show',
            'table_id' => 'exists:tables,id',
            'user_id' => 'exists:users,id',
            'is_active' => 'boolean'
        ]);

        $reservation->update($request->all());
        return response()->json($reservation->load(['user', 'table', 'creator']));
    }

    public function patch(Request $request, Reservation $reservation)
    {
        $request->validate([
            'datetime' => 'sometimes|date',
            'guests' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:pending,confirmed,seated,cancelled,no-show',
            'table_id' => 'sometimes|exists:tables,id',
            'user_id' => 'sometimes|exists:users,id',
            'is_active' => 'sometimes|boolean'
        ]);

        $reservation->update($request->only([
            'datetime', 'guests', 'status', 'table_id', 'user_id', 'is_active'
        ]));
        
        return response()->json($reservation->load(['user', 'table', 'creator']));
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return response()->json(null, 204);
    }
}

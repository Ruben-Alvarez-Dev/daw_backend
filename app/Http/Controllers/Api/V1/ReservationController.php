<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Reservation::with('user')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tables_ids' => 'nullable|array',
            'tables_ids.*' => 'exists:tables,id',
            'guests' => 'required|integer|min:1',
            'datetime' => 'required|date',
            'comments' => 'nullable|string'
        ]);

        $reservation = Reservation::create([
            'user_id' => $request->user_id,
            'tables_ids' => $request->tables_ids,
            'guests' => $request->guests,
            'datetime' => $request->datetime,
            'comments' => $request->comments,
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation created successfully',
            'data' => $reservation
        ], 201);
    }

    public function show(Reservation $reservation)
    {
        return response()->json([
            'status' => 'success',
            'data' => $reservation->load('user')
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tables_ids' => 'nullable|array',
            'tables_ids.*' => 'exists:tables,id',
            'guests' => 'required|integer|min:1',
            'datetime' => 'required|date',
            'comments' => 'nullable|string'
        ]);

        $reservation->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation updated successfully',
            'data' => $reservation
        ]);
    }

    public function patch(Request $request, Reservation $reservation)
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'tables_ids' => 'sometimes|nullable|array',
            'tables_ids.*' => 'exists:tables,id',
            'guests' => 'sometimes|integer|min:1',
            'datetime' => 'sometimes|date',
            'comments' => 'sometimes|nullable|string',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled',
            'isActive' => 'sometimes|boolean'
        ]);

        $reservation->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation updated successfully',
            'data' => $reservation
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation deleted successfully'
        ]);
    }
}

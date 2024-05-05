<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display a listing of the reservations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::all();
        if ($reservations->isEmpty()) {
            $data = [
                'message' => 'No reservations found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        return response()->json($reservations, 200);
    }

    /**
     * Store a newly created reservation in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'pax' => 'required|integer',
            'table_id' => 'nullable|exists:tables,id',
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
            return response()->json($data, 422);
        }

        $reservation = Reservation::create($request->all());
        return response()->json($reservation, 201);
    }

    /**
     * Display the specified reservation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            $data = [
                'message' => 'Reservation not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        return response()->json($reservation, 200);
    }

    /**
     * Update the specified reservation in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            $data = [
                'message' => 'Reservation not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $reservation->update($request->all());
        return response()->json($reservation, 200);
    }

    /**
     * Remove the specified reservation from the database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            $data = [
                'message' => 'Reservation not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $reservation->delete();
        return response()->json(null, 204);
    }
}
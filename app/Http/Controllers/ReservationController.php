<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;



class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::all();

            $data = [
                'reservations' => $reservations,
                'status'=> 200
            ];
            return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $reservation = new Reservation();
        $reservation->user_id = $request->user_id;
        $reservation->table_id = $request->table_id;
        $reservation->reservation_date = $request->reservation_date;
        $reservation->start_time = $request->start_time;
        $reservation->num_guests = $request->num_guests;
        $reservation->status = $request->status;
        $reservation->save();

        $data = [
            'reservation' => $reservation,
            'status' => 201
        ];
        return response()->json($data, 201);
    }

}

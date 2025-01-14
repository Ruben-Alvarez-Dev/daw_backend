<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return Reservation::all();
        }
        return Auth::user()->reservations;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'guests' => 'required|integer|min:1',
                'datetime' => 'required|date|after:now',
                'status' => 'required|in:pending,confirmed,cancelled,completed',
                'tables_ids' => 'nullable|array'
            ]);

            // Obtener el usuario de la reserva
            $user = \App\Models\User::findOrFail($request->user_id);
            
            // Crear el array de información del usuario
            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'created_at' => now()->toDateTimeString()
            ];

            $reservation = Reservation::create([
                'user_id' => $user->id,
                'tables_ids' => $request->tables_ids ?? [],
                'guests' => $request->guests,
                'datetime' => $request->datetime,
                'status' => $request->status,
                'user_info' => $userInfo
            ]);

            return response()->json([
                'message' => 'Reserva creada exitosamente',
                'reservation' => $reservation
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        return $reservation->load(['user', 'tables']);
    }

    public function update(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'datetime' => 'required|date|after:now',
            'guests' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $reservation->datetime = $request->datetime;
        $reservation->guests = $request->guests;
        $reservation->save();

        return response()->json($reservation);
    }

    public function destroy(Reservation $reservation)
    {
        // Si es admin, puede eliminar cualquier reserva
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $reservation->delete();
        return response()->json(['message' => 'Reserva eliminada con éxito']);
    }

    public function myReservations()
    {
        try {
            $reservations = Auth::user()->reservations()->orderBy('datetime', 'desc')->get();
            return response()->json($reservations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar las reservas'], 500);
        }
    }
}

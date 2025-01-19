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
                'datetime' => 'required|date',
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
        // Si es admin, puede ver cualquier reserva
        if (auth()->user()->role !== 'admin' && $reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        return $reservation->load(['user', 'tables']);
    }

    public function update(Request $request, Reservation $reservation)
    {
        // Si es admin, puede editar cualquier reserva
        if (auth()->user()->role !== 'admin' && $reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        try {
            $request->validate([
                'datetime' => 'required|date',
                'guests' => 'required|integer|min:1',
                'status' => 'required|in:pending,confirmed,cancelled,completed',
                'tables_ids' => 'nullable|array'
            ]);

            // Si es una actualización de admin, actualizar user_id si se proporciona
            if (auth()->user()->role === 'admin' && $request->has('user_id')) {
                $user = \App\Models\User::findOrFail($request->user_id);
                $reservation->user_id = $user->id;
                $reservation->user_info = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => now()->toDateTimeString()
                ];
            }

            $reservation->datetime = $request->datetime;
            $reservation->guests = $request->guests;
            $reservation->status = $request->status;
            $reservation->tables_ids = $request->tables_ids ?? [];
            $reservation->save();

            return response()->json([
                'message' => 'Reserva actualizada exitosamente',
                'reservation' => $reservation
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function getByDate($date)
    {
        try {
            // Validar el formato de fecha
            $validator = Validator::make(['date' => $date], [
                'date' => 'required|date_format:Y-m-d'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Formato de fecha inválido',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Construir el rango de fechas para el día completo
            $startDate = $date . ' 00:00:00';
            $endDate = $date . ' 23:59:59';

            // Obtener las reservas del día
            $reservations = Reservation::whereBetween('datetime', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->map(function ($reservation) {
                    // Extraer solo la hora de datetime
                    $time = date('H:i', strtotime($reservation->datetime));
                    
                    return [
                        'id' => $reservation->id,
                        'time' => $time,
                        'tables_ids' => $reservation->tables_ids,
                        'guests' => $reservation->guests,
                        'status' => $reservation->status,
                        'user_info' => $reservation->user_info,
                        'user_id' => $reservation->user_id,
                        'datetime' => $reservation->datetime
                    ];
                });

            return response()->json($reservations);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByDateAndShift(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:lunch,dinner'
        ]);

        $reservations = Reservation::whereDate('datetime', $request->date)
            ->where('shift', $request->shift)
            ->with(['user', 'table'])
            ->orderBy('datetime')
            ->get();

        return response()->json($reservations);
    }

    public function assignTable(Request $request, $id)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id'
        ]);

        $reservation = Reservation::findOrFail($id);
        
        // Verificar si la mesa está disponible para esta fecha y turno
        $existingReservation = Reservation::where('table_id', $request->table_id)
            ->whereDate('datetime', $reservation->datetime)
            ->where('shift', $reservation->shift)
            ->where('id', '!=', $id)
            ->first();

        if ($existingReservation) {
            return response()->json([
                'message' => 'La mesa ya está ocupada para este horario'
            ], 422);
        }

        $reservation->table_id = $request->table_id;
        $reservation->save();

        return response()->json($reservation->load('table'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ShiftHistory;
use App\Models\Shift;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ShiftHistoryController extends Controller
{
    public function index(Shift $shift): JsonResponse
    {
        $history = $shift->history()->with(['table', 'reservation'])->get();
        return response()->json(['history' => $history]);
    }

    public function store(Request $request, Shift $shift): JsonResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'planned_time' => 'required|date_format:H:i',
            'actual_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:planned,seated,completed,no_show,cancelled',
            'notes' => 'nullable|array'
        ]);

        // Verificar que la mesa está en la distribución del turno
        $tableInShift = $shift->distributions()
            ->where('zone', 'salon') // La mesa 1 está en la zona salon
            ->whereJsonContains('table_positions->' . $validated['table_id'], ['x' => 10, 'y' => 10])
            ->exists();

        if (!$tableInShift) {
            return response()->json([
                'message' => 'Table is not assigned to this shift'
            ], 422);
        }

        // Verificar que la hora está dentro de los slots disponibles
        if (!in_array($validated['planned_time'], $shift->slots)) {
            return response()->json([
                'message' => 'Invalid time slot'
            ], 422);
        }

        // Si hay una reserva, verificar que pertenece al mismo día
        if (isset($validated['reservation_id']) && $validated['reservation_id']) {
            $reservation = Reservation::find($validated['reservation_id']);
            if (Carbon::parse($reservation->date)->format('Y-m-d') !== $shift->date) {
                return response()->json([
                    'message' => 'Reservation date does not match shift date'
                ], 422);
            }
        }

        $history = $shift->history()->create($validated);
        return response()->json([
            'message' => 'History record created successfully',
            'history' => $history
        ], 201);
    }

    public function show(Shift $shift, ShiftHistory $history): JsonResponse
    {
        if ($history->shift_id !== $shift->id) {
            return response()->json([
                'message' => 'History record does not belong to this shift'
            ], 422);
        }

        $history->load(['table', 'reservation']);
        return response()->json(['history' => $history]);
    }

    public function update(Request $request, Shift $shift, ShiftHistory $history): JsonResponse
    {
        if ($history->shift_id !== $shift->id) {
            return response()->json([
                'message' => 'History record does not belong to this shift'
            ], 422);
        }

        $validated = $request->validate([
            'actual_time' => 'nullable|date_format:H:i',
            'status' => 'in:planned,seated,completed,no_show,cancelled',
            'notes' => 'nullable|array'
        ]);

        // Si se está marcando como seated, registrar la hora actual si no se proporciona
        if (isset($validated['status']) && $validated['status'] === 'seated' && !isset($validated['actual_time'])) {
            $validated['actual_time'] = Carbon::now()->format('H:i');
        }

        $history->update($validated);
        return response()->json([
            'message' => 'History record updated successfully',
            'history' => $history
        ]);
    }

    public function destroy(Shift $shift, ShiftHistory $history): JsonResponse
    {
        if ($history->shift_id !== $shift->id) {
            return response()->json([
                'message' => 'History record does not belong to this shift'
            ], 422);
        }

        // No permitir eliminar registros completados o no-show
        if (in_array($history->status, ['completed', 'no_show'])) {
            return response()->json([
                'message' => 'Cannot delete completed or no-show records'
            ], 422);
        }

        $history->delete();
        return response()->json([
            'message' => 'History record deleted successfully'
        ]);
    }

    public function getTableHistory(Table $table, string $date): JsonResponse
    {
        $history = ShiftHistory::where('table_id', $table->id)
            ->whereHas('shift', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->with(['shift', 'reservation'])
            ->get();

        return response()->json(['history' => $history]);
    }

    public function getReservationHistory(Reservation $reservation): JsonResponse
    {
        $history = $reservation->shiftHistory()
            ->with(['shift', 'table'])
            ->get();

        return response()->json(['history' => $history]);
    }
}

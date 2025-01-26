<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftDistribution;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index(): JsonResponse
    {
        $shifts = Shift::with('distributions')->get();
        return response()->json(['shifts' => $shifts]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'period' => 'required|in:afternoon,evening',
            'slots' => 'required|array'
        ]);

        // Verificar que no existe un turno para esa fecha y periodo
        $exists = Shift::where('date', $validated['date'])
                      ->where('period', $validated['period'])
                      ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A shift already exists for this date and period'
            ], 422);
        }

        $shift = Shift::create($validated);

        return response()->json([
            'message' => 'Shift created successfully',
            'shift' => $shift
        ], 201);
    }

    public function show(Shift $shift): JsonResponse
    {
        $shift->load('distributions');
        return response()->json(['shift' => $shift]);
    }

    public function update(Request $request, Shift $shift): JsonResponse
    {
        $validated = $request->validate([
            'slots' => 'array'
        ]);

        $shift->update($validated);

        return response()->json([
            'message' => 'Shift updated successfully',
            'shift' => $shift
        ]);
    }

    public function destroy(Shift $shift): JsonResponse
    {
        // No permitir eliminar turnos pasados
        if (Carbon::parse($shift->date)->isPast()) {
            return response()->json([
                'message' => 'Cannot delete past shifts'
            ], 422);
        }

        $shift->delete();
        return response()->json([
            'message' => 'Shift deleted successfully'
        ]);
    }

    public function getByDate(string $date): JsonResponse
    {
        $shifts = Shift::whereDate('date', $date)
                      ->with('distributions')
                      ->get();

        return response()->json(['shifts' => $shifts]);
    }

    public function addDistribution(Request $request, Shift $shift): JsonResponse
    {
        $validated = $request->validate([
            'map_template_id' => 'required|exists:map_templates,id',
            'zone' => 'required|in:salon,terrace',
            'table_positions' => 'required|array'
        ]);

        // Verificar que no existe una distribución para esta zona en este turno
        $exists = $shift->distributions()
                       ->where('zone', $validated['zone'])
                       ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A distribution already exists for this zone in this shift'
            ], 422);
        }

        $distribution = $shift->distributions()->create($validated);

        return response()->json([
            'message' => 'Distribution added successfully',
            'distribution' => $distribution
        ], 201);
    }

    public function updateDistribution(Request $request, Shift $shift, ShiftDistribution $distribution): JsonResponse
    {
        // Verificar que la distribución pertenece a este turno
        if ($distribution->shift_id !== $shift->id) {
            return response()->json([
                'message' => 'Distribution does not belong to this shift'
            ], 422);
        }

        $validated = $request->validate([
            'map_template_id' => 'exists:map_templates,id',
            'table_positions' => 'array'
        ]);

        $distribution->update($validated);

        return response()->json([
            'message' => 'Distribution updated successfully',
            'distribution' => $distribution
        ]);
    }
}

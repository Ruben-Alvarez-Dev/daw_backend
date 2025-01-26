<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TableController extends Controller
{
    public function index(): JsonResponse
    {
        $tables = Table::with('mapTemplate')->get();
        return response()->json(['tables' => $tables]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'map_template_id' => 'nullable|exists:map_templates,id',
            'position' => 'nullable|array',
            'active_from' => 'nullable|date',
            'active_until' => 'nullable|date|after:active_from'
        ]);

        $table = Table::create($validated);
        return response()->json([
            'message' => 'Table created successfully',
            'table' => $table
        ], 201);
    }

    public function show(Table $table): JsonResponse
    {
        $table->load('mapTemplate');
        return response()->json(['table' => $table]);
    }

    public function update(Request $request, Table $table): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'capacity' => 'sometimes|required|integer|min:1',
            'map_template_id' => 'nullable|exists:map_templates,id',
            'position' => 'nullable|array',
            'active_from' => 'nullable|date',
            'active_until' => 'nullable|date|after:active_from'
        ]);

        $table->update($validated);
        return response()->json([
            'message' => 'Table updated successfully',
            'table' => $table
        ]);
    }

    public function destroy(Table $table): JsonResponse
    {
        // En lugar de eliminar, establecemos active_until a la fecha actual
        $table->update([
            'active_until' => now()
        ]);

        return response()->json([
            'message' => 'Table deactivated successfully',
            'table' => $table
        ]);
    }

    public function getActiveByTemplate(string $templateId, string $date = null): JsonResponse
    {
        $date = $date ?? Carbon::today()->format('Y-m-d');

        $tables = Table::where('map_template_id', $templateId)
            ->where('active_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('active_until')
                      ->orWhere('active_until', '>=', $date);
            })
            ->get();

        return response()->json(['tables' => $tables]);
    }
}

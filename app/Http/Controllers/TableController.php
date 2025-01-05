<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $tables = Table::with('creator')->get();
        return response()->json($tables);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1'
        ]);

        $table = Table::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'created_by' => auth()->id()
        ]);

        return response()->json($table, 201);
    }

    public function show(Table $table)
    {
        return response()->json($table->load('creator'));
    }

    public function update(Request $request, $id)
    {
        try {
            $table = Table::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        \Log::info('Table update request:', [
            'request_data' => $request->all(),
            'table_id' => $table->id
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ]);

        if (!$table->update($validated)) {
            return response()->json(['error' => 'Failed to update table'], 500);
        }

        $table->refresh();
        
        \Log::info('Table updated:', [
            'table' => $table->toArray()
        ]);

        return response()->json($table->load('creator'));
    }

    public function patch(Request $request, $id)
    {
        try {
            $table = Table::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        \Log::info('Table patch request:', [
            'request_data' => $request->all(),
            'table_id' => $table->id
        ]);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean'
        ]);

        if (!$table->update($validated)) {
            return response()->json(['error' => 'Failed to update table'], 500);
        }

        $table->refresh();
        
        \Log::info('Table patched:', [
            'table' => $table->toArray()
        ]);

        return response()->json($table->load('creator'));
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return response()->json(null, 204);
    }
}

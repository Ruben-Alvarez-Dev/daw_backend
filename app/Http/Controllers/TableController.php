<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the tables.
     */
    public function index()
    {
        $tables = Table::all();
        return response()->json($tables);
    }

    /**
     * Store a newly created table in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'status' => 'sometimes|in:free,scheduled,occupied',
        ]);

        $table = Table::create($validatedData);
        return response()->json($table, 201);
    }

    /**
     * Display the specified table.
     */
    public function show(Table $table)
    {
        return response()->json($table);
    }

    /**
     * Update the specified table in storage.
     */
    public function update(Request $request, Table $table)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string',
            'capacity' => 'sometimes|integer',
            'status' => 'sometimes|in:free,scheduled,occupied',
        ]);

        $table->update($validatedData);
        return response()->json($table);
    }

    /**
     * Remove the specified table from storage.
     */
    public function destroy(Table $table)
    {
        $table->delete();
        return response()->json(null, 204);
    }
}
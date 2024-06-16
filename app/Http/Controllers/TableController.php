<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    
    public function index()
    {
        $tables = Table::all();
        return response()->json($tables);
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'status' => 'sometimes|in:available,scheduled,seated',
        ]);

        $table = Table::create($validatedData);
        return response()->json($table, 201);
    }

    
    public function show(Table $table)
    {
        return response()->json($table);
    }

    
    public function update(Request $request, Table $table)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string',
            'capacity' => 'sometimes|integer',
            'status' => 'sometimes|in:available,scheduled,seated',
        ]);

        $table->update($validatedData);
        return response()->json($table);
    }

 
    public function destroy(Table $table)
    {
        $table->delete();
        return response()->json(null, 204);
    }
}
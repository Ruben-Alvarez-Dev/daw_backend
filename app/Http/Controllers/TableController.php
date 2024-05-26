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
            'number' => 'required|unique:tables',
            'capacity' => 'required|in:2,4,8,10',
            'status' => 'required|in:pending,confirmed,canceled',
        ]);

        $table = Table::create($validatedData);
        return response()->json($table, 201);
    }

    public function show($id)
    {
        $table = Table::findOrFail($id);
        return response()->json($table);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'number' => 'required|unique:tables,number,' . $id,
            'capacity' => 'required|in:2,4,8,10',
            'status' => 'required|in:pending,confirmed,canceled',
        ]);

        $table = Table::findOrFail($id);
        $table->update($validatedData);

        return response()->json($table);
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return response()->json(['message' => 'Mesa eliminada correctamente']);
    }
}
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
            'number' => 'required|unique:tables,number',
            'capacity' => 'required',
        ]);

        $table = Table::create($validatedData);
        return response()->json($table, 201);
    }

    public function show($id)
    {
        $table = Table::findOrFail($id);
        return response()->json($table);
    }

    public function update(Request $request, $table_id)
    {
        $table = Table::findOrFail($table_id);
        $table->update($request->all());

        return response()->json($table);
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return response()->json(['message' => 'Mesa eliminada correctamente']);
    }
}
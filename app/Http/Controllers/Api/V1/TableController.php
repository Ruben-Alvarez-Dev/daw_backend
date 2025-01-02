<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Table::all()
        ]);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Table created successfully',
            'data' => $table
        ], 201);
    }

    public function show(Table $table)
    {
        return response()->json([
            'status' => 'success',
            'data' => $table
        ]);
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1'
        ]);

        $table->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Table updated successfully',
            'data' => $table
        ]);
    }

    public function patch(Request $request, Table $table)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'isActive' => 'sometimes|boolean'
        ]);

        $table->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Table updated successfully',
            'data' => $table
        ]);
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Table deleted successfully'
        ]);
    }
}

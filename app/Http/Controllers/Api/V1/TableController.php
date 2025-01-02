<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('admin')->only(['store', 'update', 'destroy']);
    }

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
            'number' => 'required|integer|unique:tables',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved'
        ]);

        $table = Table::create([
            'number' => $request->number,
            'capacity' => $request->capacity,
            'status' => $request->status,
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
            'capacity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:available,occupied,reserved'
        ]);

        $table->update($request->only(['capacity', 'status']));

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

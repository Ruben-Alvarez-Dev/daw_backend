<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Display a listing of the tables.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = Table::all();
        if ($tables->isEmpty()) {
            $data = [
                'message' => 'No tables found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        return response()->json($tables, 200);
    }

    /**
     * Store a newly created table in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_number' => 'required|unique:tables',
            'max_capacity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
            return response()->json($data, 422);
        }

        $table = Table::create($request->all());
        return response()->json($table, 201);
    }

    /**
     * Display the specified table.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $table = Table::find($id);

        if (!$table) {
            $data = [
                'message' => 'Table not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        return response()->json($table, 200);
    }

    /**
     * Update the specified table in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $table = Table::find($id);

        if (!$table) {
            $data = [
                'message' => 'Table not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $table->update($request->all());
        return response()->json($table, 200);
    }

    /**
     * Remove the specified table from the database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $table = Table::find($id);

        if (!$table) {
            $data = [
                'message' => 'Table not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $table->delete();
        return response()->json(null, 204);
    }
}
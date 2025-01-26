<?php

namespace App\Http\Controllers;

use App\Models\ShiftZoneTable;
use App\Http\Requests\ShiftZoneTableRequest;
use App\Http\Resources\ShiftZoneTableResource;

class ShiftZoneTableController extends Controller
{
    public function index()
    {
        $tables = ShiftZoneTable::with(['table', 'shiftZone'])->get();
        return ShiftZoneTableResource::collection($tables);
    }

    public function store(ShiftZoneTableRequest $request)
    {
        $table = ShiftZoneTable::create($request->validated());
        return new ShiftZoneTableResource($table);
    }

    public function show(ShiftZoneTable $table)
    {
        return new ShiftZoneTableResource($table->load(['table', 'shiftZone']));
    }

    public function update(ShiftZoneTableRequest $request, ShiftZoneTable $table)
    {
        $table->update($request->validated());
        return new ShiftZoneTableResource($table);
    }

    public function destroy(ShiftZoneTable $table)
    {
        $table->delete();
        return response()->noContent();
    }

    public function byShiftZone(ShiftZone $shiftZone)
    {
        $tables = $shiftZone->tables()->with('table')->get();
        return ShiftZoneTableResource::collection($tables);
    }
}

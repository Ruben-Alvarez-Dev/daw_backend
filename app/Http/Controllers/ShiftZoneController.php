<?php

namespace App\Http\Controllers;

use App\Models\ShiftZone;
use App\Http\Requests\ShiftZoneRequest;
use App\Http\Resources\ShiftZoneResource;

class ShiftZoneController extends Controller
{
    public function index()
    {
        $shiftZones = ShiftZone::with(['shift', 'zone', 'map'])->get();
        return ShiftZoneResource::collection($shiftZones);
    }

    public function store(ShiftZoneRequest $request)
    {
        $shiftZone = ShiftZone::create($request->validated());
        return new ShiftZoneResource($shiftZone);
    }

    public function show(ShiftZone $shiftZone)
    {
        return new ShiftZoneResource($shiftZone->load(['shift', 'zone', 'map', 'tables.table']));
    }

    public function update(ShiftZoneRequest $request, ShiftZone $shiftZone)
    {
        $shiftZone->update($request->validated());
        return new ShiftZoneResource($shiftZone);
    }

    public function destroy(ShiftZone $shiftZone)
    {
        $shiftZone->delete();
        return response()->noContent();
    }
}

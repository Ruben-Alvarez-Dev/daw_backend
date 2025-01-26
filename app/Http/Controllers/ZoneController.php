<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Http\Requests\ZoneRequest;
use App\Http\Resources\ZoneResource;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::with(['defaultMap', 'maps'])->get();
        return ZoneResource::collection($zones);
    }

    public function store(ZoneRequest $request)
    {
        $zone = Zone::create($request->validated());
        return new ZoneResource($zone);
    }

    public function show(Zone $zone)
    {
        return new ZoneResource($zone->load(['defaultMap', 'maps']));
    }

    public function update(ZoneRequest $request, Zone $zone)
    {
        $zone->update($request->validated());
        return new ZoneResource($zone);
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Map;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        return response()->json(Map::first());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'layout_data' => 'required|array'
        ]);

        // Solo permitimos un mapa
        $map = Map::first();
        if ($map) {
            $map->update($validated);
        } else {
            $map = Map::create($validated);
        }

        return response()->json($map);
    }

    public function update(Request $request, Map $map)
    {
        $validated = $request->validate([
            'layout_data' => 'required|array'
        ]);

        $map->update($validated);

        return response()->json($map);
    }
}

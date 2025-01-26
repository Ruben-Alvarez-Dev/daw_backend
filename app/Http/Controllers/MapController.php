<?php

namespace App\Http\Controllers;

use App\Models\Map;
use App\Models\MapHistory;
use App\Http\Requests\MapRequest;
use App\Http\Resources\MapResource;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function index()
    {
        $maps = Map::with(['zone'])->get();
        return MapResource::collection($maps);
    }

    public function store(MapRequest $request)
    {
        $map = Map::create($request->validated());
        
        // Create history record
        MapHistory::create([
            'map_id' => $map->id,
            'user_id' => Auth::id(),
            'content' => $map->content,
            'action' => 'create'
        ]);

        return new MapResource($map);
    }

    public function show(Map $map)
    {
        return new MapResource($map->load(['zone', 'history']));
    }

    public function update(MapRequest $request, Map $map)
    {
        // Create history record before update
        MapHistory::create([
            'map_id' => $map->id,
            'user_id' => Auth::id(),
            'content' => $map->content,
            'action' => 'update'
        ]);

        $map->update($request->validated());
        return new MapResource($map);
    }

    public function destroy(Map $map)
    {
        // Create history record before delete
        MapHistory::create([
            'map_id' => $map->id,
            'user_id' => Auth::id(),
            'content' => $map->content,
            'action' => 'delete'
        ]);

        $map->delete();
        return response()->noContent();
    }

    public function history(Map $map)
    {
        return MapHistoryResource::collection(
            $map->history()->with('user')->orderBy('created_at', 'desc')->get()
        );
    }
}

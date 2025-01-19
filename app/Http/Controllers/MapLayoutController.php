<?php

namespace App\Http\Controllers;

use App\Models\MapLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapLayoutController extends Controller
{
    public function index()
    {
        return MapLayout::all();
    }

    public function store(Request $request)
    {
        try {
            Log::info('Received request data:', $request->all());

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'layout' => 'required|array',
                'is_default' => 'boolean'
            ]);

            // Si es default, quitamos el default de otros layouts
            if ($request->input('is_default', false)) {
                DB::table('map_layouts')
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $mapLayout = MapLayout::create([
                'name' => $validated['name'],
                'layout' => $validated['layout'],
                'is_default' => $request->input('is_default', false)
            ]);

            Log::info('Layout created:', ['id' => $mapLayout->id]);
            
            return response()->json($mapLayout, 201);
        } catch (\Exception $e) {
            Log::error('Error saving layout:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error saving layout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDefault()
    {
        return MapLayout::where('is_default', true)->first();
    }

    public function setDefault($id)
    {
        DB::transaction(function () use ($id) {
            // Quitar default de todos
            DB::table('map_layouts')
                ->where('is_default', true)
                ->update(['is_default' => false]);

            // Establecer el nuevo default
            MapLayout::findOrFail($id)
                ->update(['is_default' => true]);
        });

        return response()->json(['message' => 'Default layout updated']);
    }

    public function destroy($id)
    {
        $layout = MapLayout::findOrFail($id);
        if ($layout->is_default) {
            return response()->json(['message' => 'Cannot delete default layout'], 400);
        }
        $layout->delete();
        return response()->json(['message' => 'Layout deleted']);
    }
}

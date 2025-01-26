<?php

namespace App\Http\Controllers;

use App\Models\MapTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MapTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        $templates = MapTemplate::all();
        return response()->json(['templates' => $templates]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'zone' => 'required|string|in:salon,terrace',
            'is_default' => 'boolean',
            'elements' => 'required|array'
        ]);

        // Si es default, quitamos el default anterior de esa zona
        if ($validated['is_default']) {
            MapTemplate::where('zone', $validated['zone'])
                      ->where('is_default', true)
                      ->update(['is_default' => false]);
        }

        $template = MapTemplate::create($validated);
        return response()->json([
            'message' => 'Template created successfully',
            'template' => $template
        ], 201);
    }

    public function show(MapTemplate $mapTemplate): JsonResponse
    {
        return response()->json(['template' => $mapTemplate]);
    }

    public function update(Request $request, MapTemplate $mapTemplate): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string',
            'zone' => 'string|in:salon,terrace',
            'is_default' => 'boolean',
            'elements' => 'array'
        ]);

        // Si se está estableciendo como default
        if (isset($validated['is_default']) && $validated['is_default']) {
            MapTemplate::where('zone', $validated['zone'] ?? $mapTemplate->zone)
                      ->where('is_default', true)
                      ->update(['is_default' => false]);
        }

        $mapTemplate->update($validated);
        return response()->json([
            'message' => 'Template updated successfully',
            'template' => $mapTemplate
        ]);
    }

    public function destroy(MapTemplate $mapTemplate): JsonResponse
    {
        // No permitir eliminar una plantilla default
        if ($mapTemplate->is_default) {
            return response()->json([
                'message' => 'Cannot delete default template'
            ], 422);
        }

        $mapTemplate->delete();
        return response()->json([
            'message' => 'Template deleted successfully'
        ]);
    }

    public function getDefaultTemplate(string $zone): JsonResponse
    {
        $template = MapTemplate::where('zone', $zone)
                             ->where('is_default', true)
                             ->first();

        if (!$template) {
            return response()->json([
                'message' => 'No default template found for this zone'
            ], 404);
        }

        return response()->json(['template' => $template]);
    }
}

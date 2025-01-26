<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        return response()->json(Template::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'layout_data' => 'required|array',
            'is_default' => 'boolean',
            'shift_date' => 'date|nullable',
            'shift_type' => 'in:tarde,noche|nullable'
        ]);

        // Si es default, quitamos el default de otros templates
        if ($validated['is_default']) {
            Template::where('is_default', true)->update(['is_default' => false]);
        }

        $template = Template::create($validated);

        return response()->json($template);
    }

    public function show(Template $template)
    {
        return response()->json($template);
    }

    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'layout_data' => 'required|array',
            'is_default' => 'boolean',
            'shift_date' => 'date|nullable',
            'shift_type' => 'in:tarde,noche|nullable'
        ]);

        // Si es default, quitamos el default de otros templates
        if ($validated['is_default']) {
            Template::where('id', '!=', $template->id)
                   ->where('is_default', true)
                   ->update(['is_default' => false]);
        }

        $template->update($validated);

        return response()->json($template);
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return response()->json(['message' => 'Template deleted']);
    }

    public function getForShift(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:tarde,noche'
        ]);

        // Primero buscamos un template específico para este turno
        $template = Template::forShift($validated['date'], $validated['type'])->first();
        
        // Si no hay específico, devolvemos el default
        if (!$template) {
            $template = Template::default()->first();
        }

        return response()->json($template);
    }
}

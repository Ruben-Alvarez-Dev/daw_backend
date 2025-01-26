<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'content' => 'required|array',
            'content.*.id' => 'required|string',
            'content.*.type' => 'required|string|in:wall,window,column,vegetation,surface',
            'content.*.x' => 'required|numeric',
            'content.*.y' => 'required|numeric',
            'content.*.width' => 'required|numeric|min:20',
            'content.*.height' => 'required|numeric|min:20',
            'content.*.rotation' => 'numeric'
        ];
    }
}

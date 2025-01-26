<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shift_id' => 'required|exists:shifts,id',
            'zone_id' => 'required|exists:zones,id',
            'map_id' => 'required|exists:maps,id'
        ];
    }
}

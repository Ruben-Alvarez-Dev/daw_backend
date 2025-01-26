<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftZoneTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shift_zone_id' => 'required|exists:shift_zones,id',
            'table_id' => 'required|exists:tables,id',
            'position' => 'required|array',
            'position.x' => 'required|numeric',
            'position.y' => 'required|numeric',
            'position.rotation' => 'numeric',
            'is_available' => 'boolean'
        ];
    }
}

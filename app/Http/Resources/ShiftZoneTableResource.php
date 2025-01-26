<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftZoneTableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shift_zone_id' => $this->shift_zone_id,
            'table_id' => $this->table_id,
            'position' => $this->position,
            'is_available' => $this->is_available,
            'table' => new TableResource($this->whenLoaded('table')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

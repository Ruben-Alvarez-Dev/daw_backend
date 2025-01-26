<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shift_id' => $this->shift_id,
            'zone_id' => $this->zone_id,
            'map_id' => $this->map_id,
            'shift' => new ShiftResource($this->whenLoaded('shift')),
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'map' => new MapResource($this->whenLoaded('map')),
            'tables' => ShiftZoneTableResource::collection($this->whenLoaded('tables')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

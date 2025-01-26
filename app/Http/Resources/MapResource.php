<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'zone_id' => $this->zone_id,
            'content' => $this->content,
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'history' => MapHistoryResource::collection($this->whenLoaded('history')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicle_id,
            'path' => $this->path,
            'is_cover' => $this->is_cover,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'placa' => $this->placa,
            'chassi' => $this->chassi,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'versao' => $this->versao,
            'valor_venda' => $this->valor_venda,
            'cor' => $this->cor,
            'km' => $this->km,
            'cambio' => $this->cambio,
            'combustivel' => $this->combustivel,
            'created_by' => UserResource::make($this->whenLoaded('owner')),
            'created_at' => $this->created_at,
            'updated_by' => UserResource::make($this->whenLoaded('updater')),
            'updated_at' => $this->updated_at,
            'images' => VehicleImageResource::collection($this->whenLoaded('images')),
            'image' => VehicleImageResource::make($this->whenLoaded('coverImage')),
        ];
    }
}

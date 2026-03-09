<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
    public static $wrap = 'data';

    public function with($request): array
    {
        return [
            'success' => true,
        ];
    }
}

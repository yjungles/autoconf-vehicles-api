<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiCollection extends ResourceCollection
{
    public function with($request): array
    {
        return [
            'success' => true
        ];
    }
}

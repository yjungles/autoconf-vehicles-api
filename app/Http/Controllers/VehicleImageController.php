<?php

namespace App\Http\Controllers;

use App\Services\VehicleImageService;
use App\Http\Requests\VehicleImageRequest;
use Illuminate\Http\JsonResponse;

class VehicleImageController extends Controller
{
    public function __construct(
        private readonly VehicleImageService $imageService
    ) {
    }

    public function store(VehicleImageRequest $request, int $vehicleId): JsonResponse
    {
        $images = $this->imageService->uploadImages(
            $vehicleId,
            $request->file('files')
        );

        return response()->json($images, 201);
    }

    public function setCover(int $vehicleId, int $imageId): JsonResponse
    {
        $this->imageService->setCoverImage($vehicleId, $imageId);

        return response()->json(['message' => 'Cover updated successfully']);
    }

    public function destroy(int $vehicleId, int $imageId): JsonResponse
    {
        $this->imageService->deleteImage($vehicleId, $imageId);

        return response()->json(['message' => 'Image deleted successfully']);
    }
}

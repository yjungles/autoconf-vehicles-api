<?php

use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Services\VehicleImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('mantém apenas uma imagem como capa ao definir nova capa', function () {
    $vehicle = Vehicle::factory()->create();

    $firstImage = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'is_cover' => true,
    ]);

    $secondImage = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'is_cover' => false,
    ]);

    $service = app(VehicleImageService::class);

    $service->setCoverImage($vehicle->id, $secondImage->id);

    expect($firstImage->fresh()->is_cover)->toBeFalse();
    expect($secondImage->fresh()->is_cover)->toBeTrue();

    expect(
        VehicleImage::where('vehicle_id', $vehicle->id)
            ->where('is_cover', true)
            ->count()
    )->toBe(1);
});

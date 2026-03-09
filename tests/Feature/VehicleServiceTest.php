<?php

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Services\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('lista veículos usando busca global, filtros, ordenação e paginação', function () {
    Vehicle::factory()->create([
        'placa' => 'ABC1D23',
        'marca' => 'Ford',
        'modelo' => 'Ka',
    ]);

    Vehicle::factory()->create([
        'placa' => 'BRA2E19',
        'marca' => 'Volkswagen',
        'modelo' => 'Gol',
    ]);

    Vehicle::factory()->create([
        'placa' => 'XYZ9K88',
        'marca' => 'Volkswagen',
        'modelo' => 'Polo',
    ]);

    $request = Request::create('/api/vehicles', 'GET', [
        'q' => 'Volks',
        'modelo' => 'Po',
        'sort' => '-id',
        'per_page' => 10,
        'page' => 1,
    ]);

    $service = app(VehicleService::class);

    $result = $service->list($request);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->modelo)->toBe('Polo');
});

it('respeita o limite máximo de per_page', function () {
    Vehicle::factory()->count(120)->create();

    $request = Request::create('/api/vehicles', 'GET', [
        'per_page' => 999,
    ]);

    $service = app(VehicleService::class);

    $result = $service->list($request);

    expect($result->perPage())->toBe(100);
});

it('remove veículo, imagens e arquivos do storage', function () {
    Storage::fake('public');

    config()->set('vehicles.images.disk', 'public');
    config()->set('vehicles.images.directory', 'vehicles');

    $user = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $image = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'path' => 'vehicles/' . $vehicle->id . '/foto-1.jpg',
    ]);

    Storage::disk('public')->put($image->path, 'fake-image-content');

    expect(Storage::disk('public')->exists($image->path))->toBeTrue();

    $service = app(VehicleService::class);
    $service->delete($vehicle);

    $this->assertDatabaseMissing('vehicle_images', [
        'id' => $image->id,
    ]);

    $this->assertDatabaseMissing('vehicles', [
        'id' => $vehicle->id,
    ]);

    expect(Storage::disk('public')->exists($image->path))->toBeFalse();
});

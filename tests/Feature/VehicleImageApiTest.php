<?php

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('faz upload de imagem para um veículo', function () {
    Storage::fake('public');

    config()->set('vehicles.images.disk', 'public');
    config()->set('vehicles.images.directory', 'vehicles');

    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson("/api/vehicles/{$vehicle->id}/images", [
            'files' => [
                UploadedFile::fake()->image('foto-1.jpg', 500, 500),
            ],
        ]);

    $response->assertCreated();

    $this->assertDatabaseCount('vehicle_images', 1);

    $image = VehicleImage::first();

    expect($image)->not->toBeNull();
    expect($image->vehicle_id)->toBe($vehicle->id);

    Storage::disk('public')->assertExists($image->path);
});

it('define a primeira imagem enviada como capa automaticamente', function () {
    Storage::fake('public');

    config()->set('vehicles.images.disk', 'public');
    config()->set('vehicles.images.directory', 'vehicles');

    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson("/api/vehicles/{$vehicle->id}/images", [
            'files' => [
                UploadedFile::fake()->image('foto-capa.jpg', 500, 500),
            ],
        ]);

    $response->assertCreated();

    $image = VehicleImage::first();

    expect($image)->not->toBeNull();
    expect($image->is_cover)->toBeTrue();

    expect(
        VehicleImage::where('vehicle_id', $vehicle->id)
            ->where('is_cover', true)
            ->count()
    )->toBe(1);
});

it('valida que o upload exige pelo menos uma imagem', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson("/api/vehicles/{$vehicle->id}/images", [
            'files' => [],
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['files']);
});

it('valida que o arquivo enviado deve ser imagem', function () {
    Storage::fake('public');

    config()->set('vehicles.images.disk', 'public');
    config()->set('vehicles.images.directory', 'vehicles');

    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson("/api/vehicles/{$vehicle->id}/images", [
            'files' => [
                UploadedFile::fake()->create('arquivo.pdf', 100, 'application/pdf'),
            ],
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['files.0']);
});

it('valida tamanho máximo de imagem', function () {
    Storage::fake('public');

    config()->set('vehicles.images.disk', 'public');
    config()->set('vehicles.images.directory', 'vehicles');
    config()->set('vehicles.images.max_size_kb', 1);

    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson("/api/vehicles/{$vehicle->id}/images", [
            'files' => [
                UploadedFile::fake()->image('foto-1.jpg', 500, 500),
            ],
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['files.0']);
});

it('define apenas uma imagem como capa ao trocar a capa', function () {
    $user = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $firstImage = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'is_cover' => true,
    ]);

    $secondImage = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'is_cover' => false,
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->patchJson("/api/vehicles/{$vehicle->id}/images/{$secondImage->id}/cover");

    $response->assertOk();

    expect($firstImage->fresh()->is_cover)->toBeFalse();
    expect($secondImage->fresh()->is_cover)->toBeTrue();

    expect(
        VehicleImage::where('vehicle_id', $vehicle->id)
            ->where('is_cover', true)
            ->count()
    )->toBe(1);
});

it('remove imagem do banco e do storage', function () {
    Storage::fake('public');

    config()->set('vehicles.images.disk', 'public');
    config()->set('vehicles.images.directory', 'vehicles');

    $user = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $image = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'path' => "vehicles/{$vehicle->id}/foto-1.jpg",
    ]);

    Storage::disk('public')->put($image->path, 'conteudo-fake');

    $response = $this
        ->actingAs($user, 'sanctum')
        ->deleteJson("/api/vehicles/{$vehicle->id}/images/{$image->id}");

    $response->assertOk();

    $this->assertDatabaseMissing('vehicle_images', [
        'id' => $image->id,
    ]);

    Storage::disk('public')->assertMissing($image->path);
});

it('define outra imagem como capa ao excluir a capa atual', function () {
    Storage::fake('public');

    config()->set('vehicles.images.disk', 'public');
    config()->set('vehicles.images.directory', 'vehicles');

    $user = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $coverImage = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'path' => "vehicles/{$vehicle->id}/foto-capa.jpg",
        'is_cover' => true,
    ]);

    $nextImage = VehicleImage::factory()->create([
        'vehicle_id' => $vehicle->id,
        'path' => "vehicles/{$vehicle->id}/foto-2.jpg",
        'is_cover' => false,
    ]);

    Storage::disk('public')->put($coverImage->path, 'conteudo-capa');
    Storage::disk('public')->put($nextImage->path, 'conteudo-secundaria');

    $response = $this
        ->actingAs($user, 'sanctum')
        ->deleteJson("/api/vehicles/{$vehicle->id}/images/{$coverImage->id}");

    $response->assertOk();

    $this->assertDatabaseMissing('vehicle_images', [
        'id' => $coverImage->id,
    ]);

    expect($nextImage->fresh()->is_cover)->toBeTrue();

    expect(
        VehicleImage::where('vehicle_id', $vehicle->id)
            ->where('is_cover', true)
            ->count()
    )->toBe(1);
});

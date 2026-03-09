<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleImageService
{
    private string $disk;
    private string $directory;

    public function __construct()
    {
        $this->disk = config('vehicles.images.disk');
        $this->directory = config('vehicles.images.directory');
    }

    /**
     * Upload de múltiplas imagens para um veículo
     */
    public function uploadImages(int $vehicleId, array $files): Collection
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        return collect($files)->map(function ($file) use ($vehicle) {
            return $this->uploadSingleImage($vehicle, $file);
        });
    }

    /**
     * Upload de uma única imagem
     */
    private function uploadSingleImage(Vehicle $vehicle, UploadedFile $file): VehicleImage
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = Str::ulid().'.'.$extension;
        $path = $file->storeAs(
            $this->directory.'/'.$vehicle->id,
            $fileName,
            $this->disk
        );

        return $vehicle->images()->create([
            'path' => $path,
        ]);
    }

    /**
     * Define uma imagem como capa do veículo
     */
    public function setCoverImage(int $vehicleId, int $imageId): void
    {
        $vehicle = Vehicle::with('images')->findOrFail($vehicleId);

        DB::transaction(function () use ($vehicle, $imageId) {
            $vehicle->images()->update(['is_cover' => false]);
            $image = $vehicle->images()->findOrFail($imageId);
            $image->update(['is_cover' => true]);
        });
    }

    /**
     * Remove uma imagem
     */
    public function deleteImage(int $vehicleId, int $imageId): void
    {
        $image = VehicleImage::where('vehicle_id', $vehicleId)
            ->findOrFail($imageId);

        Storage::disk($this->disk)->delete($image->path);

        $image->delete();
    }
}

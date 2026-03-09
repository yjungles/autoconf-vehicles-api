<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleImageFactory extends Factory
{
    protected $model = VehicleImage::class;

    public function definition(): array
    {
        return [
            'path' => config('vehicles.images.default_image_path'),
            'is_cover' => false,
            'vehicle_id' => Vehicle::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($image) {
            $disk = config('vehicles.images.disk');
            $directory = config('vehicles.images.directory');
            $default = config('vehicles.images.default_image_path');

            $filename = Str::ulid().'.png';

            $path = "{$directory}/{$image->vehicle_id}/{$filename}";

            Storage::disk($disk)->copy($default, $path);

            $image->update([
                'path' => $path
            ]);
        });
    }
}

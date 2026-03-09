<?php

namespace App\Models;

use App\Enums\CambioEnum;
use App\Enums\CombustivelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'chassi',
        'marca',
        'modelo',
        'versao',
        'valor_venda',
        'cor',
        'km',
        'cambio',
        'combustivel',
        'user_id',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'cambio' => CambioEnum::class,
            'combustivel' => CombustivelEnum::class,
        ];
    }

    protected static function booted(): void
    {
        static::updating(function ($vehicle) {
            $vehicle->updated_by = auth()->id();
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(VehicleImage::class)->where('is_cover', true);
    }
}

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleImageController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::prefix('auth')->middleware('throttle:api')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,60');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

Route::prefix('vehicles')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/', [VehicleController::class, 'index']);
    Route::post('/', [VehicleController::class, 'store']);
    Route::get('/{id}', [VehicleController::class, 'show']);
    Route::put('/{id}', [VehicleController::class, 'update']);
    Route::patch('/{id}', [VehicleController::class, 'update']);
    Route::delete('/{id}', [VehicleController::class, 'destroy']);

    Route::prefix('/{vehicleId}/images')->group(function () {
        Route::post('/', [VehicleImageController::class, 'store']);
        Route::patch('/{imageId}/cover', [VehicleImageController::class, 'setCover']);
        Route::delete('/{imageId}', [VehicleImageController::class, 'destroy']);
    });

});

Route::get('/csrf-cookie', [CsrfCookieController::class, 'show'])->middleware('web')->name('sanctum.csrf-cookie');

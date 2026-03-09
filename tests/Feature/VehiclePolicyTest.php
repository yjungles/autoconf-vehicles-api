<?php

use App\Models\User;
use App\Models\Vehicle;
use App\Policies\VehiclePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite update quando usuário é dono do veículo', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $policy = new VehiclePolicy();

    expect($policy->update($user, $vehicle))->toBeTrue();
});

it('permite update quando usuário é admin', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $vehicle = Vehicle::factory()->create();

    $policy = new VehiclePolicy();

    expect($policy->update($admin, $vehicle))->toBeTrue();
});

it('não permite update quando usuário não é dono e não é admin', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create([
        'is_admin' => false,
    ]);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $owner->id,
    ]);

    $policy = new VehiclePolicy();

    expect($policy->update($otherUser, $vehicle))->toBeFalse();
});

it('permite delete quando usuário é dono do veículo', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $policy = new VehiclePolicy();

    expect($policy->delete($user, $vehicle))->toBeTrue();
});

it('permite delete quando usuário é admin', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $vehicle = Vehicle::factory()->create();

    $policy = new VehiclePolicy();

    expect($policy->delete($admin, $vehicle))->toBeTrue();
});

it('não permite delete quando usuário não é dono e não é admin', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create([
        'is_admin' => false,
    ]);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $owner->id,
    ]);

    $policy = new VehiclePolicy();

    expect($policy->delete($otherUser, $vehicle))->toBeFalse();
});

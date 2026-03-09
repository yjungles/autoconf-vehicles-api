<?php

use App\Enums\CambioEnum;
use App\Enums\CombustivelEnum;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function vehicleValidationPayload(array $overrides = []): array
{
    return array_merge([
        'placa' => 'BRA2E19',
        'chassi' => '9BWZZZ377VT004251',
        'marca' => 'Volkswagen',
        'modelo' => 'Gol',
        'versao' => '1.6 MSI',
        'valor_venda' => 45000.00,
        'cor' => 'Prata',
        'km' => 120000,
        'cambio' => CambioEnum::cases()[0]->value,
        'combustivel' => CombustivelEnum::cases()[0]->value,
    ], $overrides);
}

it('exige autenticação para criar veículo', function () {
    $response = $this->postJson('/api/vehicles', vehicleValidationPayload());

    $response->assertUnauthorized();
});

it('valida campos obrigatórios na criação de veículo', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson('/api/vehicles', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'placa',
            'chassi',
            'marca',
            'modelo',
            'valor_venda',
            'cor',
            'km',
            'cambio',
            'combustivel',
        ]);
});

it('valida placa duplicada na criação de veículo', function () {
    $user = User::factory()->create();

    Vehicle::factory()->create([
        'placa' => 'BRA2E19',
        'chassi' => '9BWZZZ377VT004299',
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson('/api/vehicles', vehicleValidationPayload([
            'chassi' => '9BWZZZ377VT004251',
        ]));

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['placa']);
});

it('valida chassi duplicado na criação de veículo', function () {
    $user = User::factory()->create();

    Vehicle::factory()->create([
        'placa' => 'ABC1D23',
        'chassi' => '9BWZZZ377VT004251',
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson('/api/vehicles', vehicleValidationPayload([
            'placa' => 'BRA2E19',
        ]));

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['chassi']);
});

it('permite atualizar mantendo a mesma placa e o mesmo chassi do próprio veículo', function () {
    $user = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
        'placa' => 'BRA2E19',
        'chassi' => '9BWZZZ377VT004251',
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->putJson("/api/vehicles/{$vehicle->id}", vehicleValidationPayload([
            'placa' => 'BRA2E19',
            'chassi' => '9BWZZZ377VT004251',
            'modelo' => 'Polo',
        ]));

    $response->assertOk();
});

it('valida chassi com tamanho inválido', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson('/api/vehicles', vehicleValidationPayload([
            'chassi' => '123',
        ]));

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['chassi']);
});

it('valida km negativo', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson('/api/vehicles', vehicleValidationPayload([
            'chassi' => '9BWZZZ377VT004252',
            'placa' => 'ABC1D23',
            'km' => -1,
        ]));

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['km']);
});

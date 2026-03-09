<?php

use App\Enums\CambioEnum;
use App\Enums\CombustivelEnum;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function vehiclePayload(array $overrides = []): array
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

it('lista veículos com sucesso', function () {

    $user = User::factory()->create();
    Vehicle::factory()->count(3)->create();

    $response = $this->actingAs($user)->getJson('/api/vehicles');

    $response
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
});

it('cria um veículo autenticado com sucesso', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->postJson('/api/vehicles', vehiclePayload());

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Veículo criado com sucesso.')
        ->assertJsonPath('data.placa', 'BRA2E19');

    $this->assertDatabaseHas('vehicles', [
        'placa' => 'BRA2E19',
        'user_id' => $user->id,
    ]);
});

it('exibe um veículo específico', function () {
    $vehicle = Vehicle::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/vehicles/{$vehicle->id}");

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $vehicle->id);
});

it('permite que o dono atualize o próprio veículo', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->putJson("/api/vehicles/{$vehicle->id}", vehiclePayload([
            'placa' => 'ABC1D23',
            'chassi' => '9BWZZZ377VT004252',
            'modelo' => 'Polo',
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Veículo atualizado com sucesso.')
        ->assertJsonPath('data.modelo', 'Polo');

    $this->assertDatabaseHas('vehicles', [
        'id' => $vehicle->id,
        'modelo' => 'Polo',
        'updated_by' => $user->id,
    ]);
});

it('impede que outro usuário atualize o veículo', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $owner->id,
    ]);

    $response = $this
        ->actingAs($otherUser)
        ->putJson("/api/vehicles/{$vehicle->id}", vehiclePayload([
            'placa' => 'ABC1D23',
            'chassi' => '9BWZZZ377VT004253',
        ]));

    $response->assertForbidden();
});

it('permite que admin atualize veículo de outro usuário', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $owner->id,
    ]);

    $response = $this
        ->actingAs($admin)
        ->putJson("/api/vehicles/{$vehicle->id}", vehiclePayload([
            'placa' => 'ABC1D24',
            'chassi' => '9BWZZZ377VT004254',
            'modelo' => 'Virtus',
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('data.modelo', 'Virtus');
});

it('permite que o dono exclua o próprio veículo', function () {
    $user = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->deleteJson("/api/vehicles/{$vehicle->id}");

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Veículo apagado com sucesso.');

    $this->assertDatabaseMissing('vehicles', [
        'id' => $vehicle->id,
    ]);
});

it('impede que outro usuário exclua o veículo', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $vehicle = Vehicle::factory()->create([
        'user_id' => $owner->id,
    ]);

    $response = $this
        ->actingAs($otherUser)
        ->deleteJson("/api/vehicles/{$vehicle->id}");

    $response->assertForbidden();
});

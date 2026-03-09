<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('valida obrigatoriedade de email e senha no login', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

it('valida formato de email no login', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'email-invalido',
        'password' => 'password',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

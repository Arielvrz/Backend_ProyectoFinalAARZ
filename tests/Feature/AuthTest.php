<?php

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([RoleSeeder::class, UserSeeder::class]);
});

it('allows a user to login with valid credentials and receive a token', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'admin@empresa.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['token', 'token_type', 'user']);
});

it('returns 401 when logging in with incorrect password', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'admin@empresa.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
             ->assertJsonPath('message', 'Credenciales inválidas');
});

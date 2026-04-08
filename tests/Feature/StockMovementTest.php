<?php

use App\Models\Category;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([RoleSeeder::class, UserSeeder::class]);
    
    $this->bodeguero = User::whereHas('role', fn($q) => $q->where('name', 'bodeguero'))->first();
    $this->despacho = User::whereHas('role', fn($q) => $q->where('name', 'despacho'))->first();
    
    $category = Category::create(['name' => 'Categoria']);
    $supplier = Supplier::create(['name' => 'Proveedor']);
    $unit = MeasurementUnit::create(['name' => 'Kilos', 'abbreviation' => 'kg']);

    $this->product = Product::create([
        'sku' => 'PROD-002',
        'name' => 'Test Stock',
        'price' => 10,
        'current_stock' => 20,
        'min_stock' => 10,
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'unit_id' => $unit->id,
    ]);
});

it('successfully logs an entry and increases current_stock', function () {
    $response = $this->actingAs($this->bodeguero)->postJson('/api/stock-movements', [
        'product_id' => $this->product->id,
        'type' => 'entry',
        'quantity' => 15,
        'notes' => 'Entrada de prueba'
    ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'current_stock' => 35
    ]);
});

it('returns 422 with stock insuficiente when despacho tries to exit more than available', function () {
    $response = $this->actingAs($this->despacho)->postJson('/api/stock-movements', [
        'product_id' => $this->product->id,
        'type' => 'exit',
        'quantity' => 100, // available is 20
        'notes' => 'Salida excesiva'
    ]);

    $response->assertStatus(422)
             ->assertJsonFragment(['message' => 'Stock insuficiente']);
});

it('includes a warning when exit reduces stock below min_stock', function () {
    $response = $this->actingAs($this->despacho)->postJson('/api/stock-movements', [
        'product_id' => $this->product->id,
        'type' => 'exit',
        'quantity' => 15, // leaves 5, which is < min_stock (10)
        'notes' => 'Salida que deja por debajo del mínimo'
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure(['warning']);
             
    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'current_stock' => 5
    ]);
});

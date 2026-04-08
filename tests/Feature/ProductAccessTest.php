<?php

use App\Models\Category;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([RoleSeeder::class, UserSeeder::class]);
    
    $this->admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
    $this->bodeguero = User::whereHas('role', fn($q) => $q->where('name', 'bodeguero'))->first();
    
    $this->category = Category::create(['name' => 'Categoria 1']);
    $this->supplier = Supplier::create(['name' => 'Proveedor 1']);
    $this->unit = MeasurementUnit::create(['name' => 'Kilos', 'abbreviation' => 'kg']);

    $this->productData = [
        'sku' => 'PROD-001',
        'name' => 'Producto Prueba',
        'price' => 100,
        'current_stock' => 50,
        'min_stock' => 10,
        'category_id' => $this->category->id,
        'supplier_id' => $this->supplier->id,
        'unit_id' => $this->unit->id,
    ];
});

it('allows admin to create a product', function () {
    $response = $this->actingAs($this->admin)->postJson('/api/products', $this->productData);
    $response->assertStatus(201);
    $this->assertDatabaseHas('products', ['sku' => 'PROD-001']);
});

it('returns 403 when bodeguero tries to create a product', function () {
    $response = $this->actingAs($this->bodeguero)->postJson('/api/products', $this->productData);
    $response->assertStatus(403);
});

it('allows admin to perform hard delete on product without movements', function () {
    $product = Product::create($this->productData);
    
    $response = $this->actingAs($this->admin)->deleteJson("/api/products/{$product->id}");
    
    $response->assertStatus(204);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('performs soft delete when admin deletes product with movements', function () {
    $product = Product::create($this->productData);
    
    StockMovement::create([
        'product_id' => $product->id,
        'user_id' => $this->bodeguero->id,
        'type' => 'entry',
        'quantity' => 10,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)->deleteJson("/api/products/{$product->id}");
    
    $response->assertStatus(200)
             ->assertJsonFragment(['type' => 'soft_delete']);
             
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

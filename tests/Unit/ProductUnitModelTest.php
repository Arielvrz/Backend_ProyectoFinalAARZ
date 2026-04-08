<?php

use App\Models\Category;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('soft delete does not physically remove the record from products table', function () {
    $category = Category::create(['name' => 'Categoria Unit']);
    $supplier = Supplier::create(['name' => 'Proveedor Unit']);
    $unit = MeasurementUnit::create(['name' => 'Kilos Unit', 'abbreviation' => 'kg']);

    $product = Product::create([
        'sku' => 'PROD-UNIT-001',
        'name' => 'Product Unit Test',
        'price' => 10,
        'current_stock' => 10,
        'min_stock' => 5,
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'unit_id' => $unit->id,
    ]);

    // Apply Soft Delete
    $product->delete();

    // Verify it is ignored in normal queries
    expect(Product::find($product->id))->toBeNull();
    
    // Verify it physically remains in the DB with deleted_at timestamp
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

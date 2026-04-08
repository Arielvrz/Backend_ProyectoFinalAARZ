<?php

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Category;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('StockMovement request only accepts entry or exit types', function () {
    $category = Category::create(['name' => 'Categoria Val']);
    $supplier = Supplier::create(['name' => 'Proveedor Val']);
    $unit = MeasurementUnit::create(['name' => 'Kilos Val', 'abbreviation' => 'kg']);

    $product = Product::create([
        'sku' => 'PROD-VAL-001',
        'name' => 'Product Val Test',
        'price' => 10,
        'current_stock' => 10,
        'min_stock' => 5,
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'unit_id' => $unit->id,
    ]);

    $request = new StoreStockMovementRequest();
    $rules = $request->rules();
    
    $validatorValido = Validator::make(['product_id' => $product->id, 'type' => 'entry', 'quantity' => 5], $rules);
    expect($validatorValido->passes())->toBeTrue();
    
    $validatorAunValido = Validator::make(['product_id' => $product->id, 'type' => 'exit', 'quantity' => 5], $rules);
    expect($validatorAunValido->passes())->toBeTrue();
    
    $validatorInvalido = Validator::make(['product_id' => $product->id, 'type' => 'invalid_type', 'quantity' => 5], $rules);
    expect($validatorInvalido->passes())->toBeFalse();
    expect($validatorInvalido->errors()->has('type'))->toBeTrue();
});

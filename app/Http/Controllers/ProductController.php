<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar productos",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de productos cargada exitosamente"
     *     )
     * )
     */
    public function index()
    {
        return ProductResource::collection(Product::with(['category', 'supplier', 'unit'])->paginate(15));
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear producto",
     *     description="Requiere rol de admin.",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"sku", "name", "price", "current_stock", "min_stock", "category_id", "supplier_id", "unit_id"},
     *             @OA\Property(property="sku", type="string", example="PROD-001"),
     *             @OA\Property(property="name", type="string", example="Producto Ejemplo"),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="price", type="number", format="float", example=10.50),
     *             @OA\Property(property="current_stock", type="number", format="float", example=100),
     *             @OA\Property(property="min_stock", type="number", format="float", example=20),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="supplier_id", type="integer", example=1),
     *             @OA\Property(property="unit_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Producto creado exitosamente")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);
        $product = Product::create($request->validated());
        $product->load(['category', 'supplier', 'unit']);
        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'supplier', 'unit'])->findOrFail($id);
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $product->update($request->validated());
        $product->load(['category', 'supplier', 'unit']);
        return new ProductResource($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{product}",
     *     summary="Eliminar producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto desactivado (Soft Delete) porque posee movimientos atados.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto desactivado"),
     *             @OA\Property(property="type", type="string", example="soft_delete")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Hard Delete exitoso. Sin contenido devuelto."
     *     )
     * )
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->stockMovements()->count() > 0) {
            $product->delete();
            return response()->json([
                'message' => 'Producto desactivado',
                'type' => 'soft_delete'
            ], 200);
        } else {
            $product->forceDelete();
            return response()->noContent();
        }
    }
}

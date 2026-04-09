<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage-inventory');
        $movements = StockMovement::with(['product', 'user.role'])
            ->latest('created_at')
            ->paginate(15);
            
        return StockMovementResource::collection($movements);
    }

    /**
     * @OA\Post(
     *     path="/api/stock-movements",
     *     summary="Registrar movimiento de stock",
     *     tags={"Movimientos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "type", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", enum={"entry", "exit"}, example="exit"),
     *             @OA\Property(property="quantity", type="number", format="float", example=10),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Salida por venta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movimiento registrado exitosamente. Incluye campo 'warning' si el stock baja del mínimo."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Stock insuficiente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Stock insuficiente"),
     *             @OA\Property(property="available", type="number", format="float"),
     *             @OA\Property(property="requested", type="number", format="float")
     *         )
     *     )
     * )
     */
    public function store(StoreStockMovementRequest $request)
    {
        $this->authorize('manage-inventory');

        return DB::transaction(function () use ($request) {
            // Pessimistic Locking: Bloquea la fila del producto hasta que termine la transacción
            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            if ($request->type === 'exit') {
                if ($product->current_stock < $request->quantity) {
                    // Al retornar aquí, la transacción termina sin guardar cambios
                    return response()->json([
                        'message' => 'Stock insuficiente',
                        'available' => $product->current_stock,
                        'requested' => $request->quantity
                    ], 422);
                }
                $product->current_stock -= $request->quantity;
            }

            if ($request->type === 'entry') {
                $product->current_stock += $request->quantity;
            }

            $product->save();

            $movement = StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'created_at' => now(),
            ]);

            $movement->load(['product', 'user.role']);

            $response = (new StockMovementResource($movement))->toArray(request());

            if ($product->current_stock < $product->min_stock) {
                $response['warning'] = 'Stock por debajo del mínimo. Considere reabastecer.';
            }

            return response()->json($response, 201);
        });
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toDateTimeString(),
            'product' => ['id' => $this->product->id, 'name' => $this->product->name, 'sku' => $this->product->sku],
            'registered_by' => ['id' => $this->user->id, 'name' => $this->user->name, 'role' => $this->user->role->name]
        ];
    }
}

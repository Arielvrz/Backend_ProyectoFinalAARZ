<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'current_stock' => $this->current_stock,
            'min_stock' => $this->min_stock,
            'stock_alert' => $this->current_stock < $this->min_stock,
            'category' => ['id' => $this->category->id, 'name' => $this->category->name],
            'supplier' => ['id' => $this->supplier->id, 'name' => $this->supplier->name],
            'unit' => [
                'id' => $this->unit->id, 
                'name' => $this->unit->name,
                'abbreviation' => $this->unit->abbreviation
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString()
        ];
    }
}

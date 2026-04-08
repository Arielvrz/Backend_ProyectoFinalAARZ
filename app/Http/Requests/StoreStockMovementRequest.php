<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:entry,exit',
            'quantity'   => 'required|numeric|min:0.01',
            'notes'      => 'nullable|string|max:500'
        ];
    }
}

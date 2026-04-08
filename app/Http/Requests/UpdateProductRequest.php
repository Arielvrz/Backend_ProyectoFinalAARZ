<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku'           => 'required|string|unique:products,sku,' . $this->product->id,
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'min_stock'     => 'required|numeric|min:0',
            'category_id'   => 'required|exists:categories,id',
            'supplier_id'   => 'required|exists:suppliers,id',
            'unit_id'       => 'required|exists:measurement_units,id'
        ];
    }
}

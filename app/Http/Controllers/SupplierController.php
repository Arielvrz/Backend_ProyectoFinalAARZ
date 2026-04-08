<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Supplier::all();
    }

    public function store(Request $request)
    {
        $this->authorize('is-admin');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('is-admin');

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return response()->json($supplier, 200);
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('is-admin');
        
        $supplier->delete();

        return response()->noContent();
    }
}

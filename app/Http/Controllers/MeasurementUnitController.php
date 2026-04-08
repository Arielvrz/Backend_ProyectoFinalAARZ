<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MeasurementUnitController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return MeasurementUnit::all();
    }

    public function store(Request $request)
    {
        $this->authorize('is-admin');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:255',
        ]);

        $measurementUnit = MeasurementUnit::create($validated);

        return response()->json($measurementUnit, 201);
    }

    public function show(MeasurementUnit $measurementUnit)
    {
        return $measurementUnit;
    }

    public function update(Request $request, MeasurementUnit $measurementUnit)
    {
        $this->authorize('is-admin');

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'abbreviation' => 'sometimes|string|max:255',
        ]);

        $measurementUnit->update($validated);

        return response()->json($measurementUnit, 200);
    }

    public function destroy(MeasurementUnit $measurementUnit)
    {
        $this->authorize('is-admin');
        
        $measurementUnit->delete();

        return response()->noContent();
    }
}

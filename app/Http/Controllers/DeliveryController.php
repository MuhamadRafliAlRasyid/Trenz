<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    // Get list ongkir dari RajaOngkir
    public function getShippingOptions(Request $request, RajaOngkirService $service)
    {
        $validated = $request->validate([
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer',
            'courier' => 'required|string',
        ]);

        $costs = $service->getCost(
            $validated['origin'],
            $validated['destination'],
            $validated['weight'],
            $validated['courier']
        );

        return response()->json($costs);
    }

    // Simpan pilihan kurir dari checkout
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'courier_type' => 'required|in:internal,external',
            'courier_id' => 'nullable|exists:users,id',
            'external_service' => 'nullable|string',
            'external_cost' => 'nullable|integer',
            'courier_name' => 'nullable|string',
            'courier_service' => 'nullable|string',
            'shipping_cost' => 'nullable|integer',
            'tracking_number' => 'nullable|string',
        ]);

        $delivery = Delivery::create($validated);

        return response()->json([
            'message' => 'Delivery option saved successfully',
            'data' => $delivery
        ], 201);
    }
}

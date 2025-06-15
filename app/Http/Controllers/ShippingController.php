<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    public function getCities()
    {
        $cities = $this->rajaOngkir->getCities();
        return response()->json($cities);
    }

    public function getCost(Request $request)
    {
        $request->validate([
            'origin' => 'required|numeric',
            'destination' => 'required|numeric',
            'weight' => 'required|numeric',
            'courier' => 'required|string'
        ]);

        $cost = $this->rajaOngkir->getCost(
            $request->origin,
            $request->destination,
            $request->weight,
            $request->courier
        );

        return response()->json($cost);
    }
}

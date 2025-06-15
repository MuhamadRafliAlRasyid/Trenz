<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected $key;
    protected $url;

    public function __construct()
    {
        $this->key = config('services.rajaongkir.key');
        $this->url = config('services.rajaongkir.base_url');
    }

    public function getCities()
    {
        $response = Http::withHeaders([
            'key' => $this->key
        ])->get($this->url . '/city');

        return $response->json()['rajaongkir']['results'] ?? [];
    }

    public function getCost($origin, $destination, $weight, $courier)
    {
        $response = Http::withHeaders([
            'key' => $this->key
        ])->post($this->url . '/cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        ]);

        return $response->json()['rajaongkir']['results'][0]['costs'] ?? [];
    }
}

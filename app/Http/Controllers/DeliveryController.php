<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    // Middleware khusus kurir

    // 1. Ambil semua pengiriman milik kurir yang sedang login
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'deliveries' => Delivery::where('courier_id', $user->id)->get()
        ]);
    }


    // 2. Update status pengiriman (in_progress → done)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:in_progress,done'
        ]);

        $delivery = Delivery::where('id', $id)
            ->where('courier_id', Auth::id())
            ->firstOrFail();

        $delivery->status = $request->status;
        $delivery->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    // 3. Update lokasi kurir (dari aplikasi Ionic)
    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $delivery = Delivery::where('id', $id)
            ->where('courier_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if (!$delivery) {
            return response()->json(['message' => 'No active delivery found'], 404);
        }

        $delivery->latitude = $request->lat;
        $delivery->longitude = $request->lng;
        $delivery->save();

        return response()->json(['message' => 'Location updated']);
    }

    // 4. Ambil lokasi kurir (oleh customer)
    public function getCourierLocation($orderId)
    {
        $delivery = Delivery::whereHas('transaction', function ($query) use ($orderId) {
            $query->where('id', $orderId);
        })->first();

        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }

        return response()->json([
            'lat' => $delivery->latitude,
            'lng' => $delivery->longitude,
            'status' => $delivery->status,
        ]);
    }
}

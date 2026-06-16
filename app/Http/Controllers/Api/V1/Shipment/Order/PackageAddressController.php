<?php

namespace App\Http\Controllers\Api\V1\Shipment\Order;

use App\Models\PackageAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PackageAddressController extends Controller
{
    // Get all addresses for authenticated user
    public function index(Request $request)
    {
        $addresses = PackageAddress::where('user_id', $request->user()->id)
            ->with(['city', 'state', 'country', 'zone'])
            ->orderBy('id', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->address . $item->latitude . $item->longitude . $item->type->value;
            })
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'User addresses',
            'data' => $addresses,
        ]);
    }



    // Get specific address
    public function show(Request $request, $id)
    {
        $address = PackageAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['city', 'state', 'country', 'zone'])
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Address details',
            'data' => $address,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CancelReasonResource;
use App\Models\CancelReason;
use Illuminate\Http\Request;

class CancelReasonController extends Controller
{
    /**
     * Get all cancel reasons
     */
    public function index(Request $request)
    {
        $cancelReasons = CancelReason::query()
            ->where('is_active', true) // لو عندك active flag
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CancelReasonResource::collection($cancelReasons),
        ]);
    }
}

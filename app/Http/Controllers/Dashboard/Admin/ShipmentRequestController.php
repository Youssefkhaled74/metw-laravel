<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\ShipmentRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ShipmentCompany;
use App\Models\ShipmentRequest;
use Illuminate\Http\Request;

class ShipmentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $statuses = array_map(fn($s) => $s->value, ShipmentRequestStatus::cases());

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $query = ShipmentRequest::query()
            ->with(['user', 'senderContact.primaryAddress.governorate', 'senderContact.primaryAddress.city', 'receiverContact.primaryAddress.governorate', 'receiverContact.primaryAddress.city', 'packages']);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhereHas('senderContact', function ($sq) use ($search) {
                        $sq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('primary_mobile', 'like', "%{$search}%");
                    })
                    ->orWhereHas('receiverContact', function ($rq) use ($search) {
                        $rq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('primary_mobile', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $requests = $query->latest()->paginate(20)->appends($request->query());

        return view('dashboard.admin.shipment-requests.index', compact('requests', 'statuses'));
    }

    public function show($id)
    {
        $shipmentRequest = ShipmentRequest::with([
            'user',
            'senderContact.primaryAddress.governorate',
            'senderContact.primaryAddress.city',
            'senderContact.primaryAddress.state',
            'senderContact.primaryAddress.zone',
            'receiverContact.primaryAddress.governorate',
            'receiverContact.primaryAddress.city',
            'receiverContact.primaryAddress.state',
            'receiverContact.primaryAddress.zone',
            'packages.mediaFiles',
        ])->findOrFail($id);

        return view('dashboard.admin.shipment-requests.show', compact('shipmentRequest'));
    }
}

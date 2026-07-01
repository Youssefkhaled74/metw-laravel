<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\ShipmentRequestStatus;
use App\Http\Controllers\Controller;
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
            'from_location' => ['nullable', 'string', 'max:120'],
            'to_location' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $query = ShipmentRequest::query()
            ->with([
                'user',
                'senderContact.primaryAddress.governorate',
                'senderContact.primaryAddress.city',
                'receiverContact.primaryAddress.governorate',
                'receiverContact.primaryAddress.city',
                'packages.mediaFiles',
            ])
            ->withCount('packages');

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
                    })
                    ->orWhereHas('packages', function ($pq) use ($search) {
                        $pq->where('package_name', 'like', "%{$search}%")
                            ->orWhere('package_type', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['from_location'])) {
            $fromLocation = trim($validated['from_location']);
            $query->whereHas('senderContact.primaryAddress', function ($addressQuery) use ($fromLocation) {
                $addressQuery->where(function ($locationQuery) use ($fromLocation) {
                        $locationQuery->where('address_line_1', 'like', "%{$fromLocation}%")
                            ->orWhere('address_line_2', 'like', "%{$fromLocation}%")
                            ->orWhere('landmark', 'like', "%{$fromLocation}%")
                            ->orWhereHas('city', fn ($cityQuery) => $cityQuery->where('name', 'like', "%{$fromLocation}%"))
                            ->orWhereHas('governorate', fn ($governorateQuery) => $governorateQuery->where('name', 'like', "%{$fromLocation}%"));
                });
            });
        }

        if (!empty($validated['to_location'])) {
            $toLocation = trim($validated['to_location']);
            $query->whereHas('receiverContact.primaryAddress', function ($addressQuery) use ($toLocation) {
                $addressQuery->where(function ($locationQuery) use ($toLocation) {
                        $locationQuery->where('address_line_1', 'like', "%{$toLocation}%")
                            ->orWhere('address_line_2', 'like', "%{$toLocation}%")
                            ->orWhere('landmark', 'like', "%{$toLocation}%")
                            ->orWhereHas('city', fn ($cityQuery) => $cityQuery->where('name', 'like', "%{$toLocation}%"))
                            ->orWhereHas('governorate', fn ($governorateQuery) => $governorateQuery->where('name', 'like', "%{$toLocation}%"));
                });
            });
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('dashboard.admin.shipment-requests.index', compact('requests', 'statuses'));
    }

    public function show($id)
    {
        $shipmentRequest = ShipmentRequest::with([
            'user',
            'senderContact.primaryAddress.governorate',
            'senderContact.primaryAddress.city',
            'receiverContact.primaryAddress.governorate',
            'receiverContact.primaryAddress.city',
            'packages.mediaFiles',
        ])->findOrFail($id);

        return view('dashboard.admin.shipment-requests.show', compact('shipmentRequest'));
    }
}

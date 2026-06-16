<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\ShipmentCompany;
use App\Models\ShipmentLocation;
use App\Models\VendorBranch;
use App\Enum\OrderStatus;

class OrderController extends Controller
{
    public function index()
    {
        $vendorId = auth('vendor')->id();

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'in:order_number,customer,your_items,your_total,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $ordersQuery = EcommerceOrder::query()
            ->where('status', '!=', OrderStatus::PENDING)
            ->whereHas('items.product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->with([
                'user',
                'items' => function ($query) use ($vendorId) {
                    $query->whereHas('product', function ($q) use ($vendorId) {
                        $q->where('vendor_id', $vendorId);
                    })->with(['product', 'pickupBranch', 'shipmentCompany']);
                }
            ])
            ->withCount([
                'items as vendor_items_count' => function ($query) use ($vendorId) {
                    $query->whereHas('product', function ($productQuery) use ($vendorId) {
                        $productQuery->where('vendor_id', $vendorId);
                    });
                }
            ])
            ->withSum([
                'items as vendor_total_sum' => function ($query) use ($vendorId) {
                    $query->whereHas('product', function ($productQuery) use ($vendorId) {
                        $productQuery->where('vendor_id', $vendorId);
                    });
                }
            ], 'final_price');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $ordersQuery->where(function ($query) use ($search, $vendorId) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.product', function ($productQuery) use ($search, $vendorId) {
                        $productQuery->where('vendor_id', $vendorId)
                            ->where(function ($nestedQuery) use ($search) {
                                $nestedQuery->where('product_number', 'like', "%{$search}%")
                                    ->orWhere('sku', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $ordersQuery->where('status', $validated['status']);
        }

        if ($sortBy === 'customer') {
            $ordersQuery->leftJoin('users as u', 'ecommerce_orders.user_id', '=', 'u.id')
                ->select('ecommerce_orders.*')
                ->orderBy('u.username', $sortDir);
        } elseif ($sortBy === 'your_items') {
            $ordersQuery->orderBy('vendor_items_count', $sortDir);
        } elseif ($sortBy === 'your_total') {
            $ordersQuery->orderBy('vendor_total_sum', $sortDir);
        } else {
            $ordersQuery->orderBy("ecommerce_orders.{$sortBy}", $sortDir);
        }

        $orders = $ordersQuery
            ->paginate(10)
            ->appends(request()->query());

        // Calculate vendor-specific totals using helper method
        foreach ($orders as $order) {
            $totals = $order->getVendorTotals($vendorId);

            // Add totals as properties for easy access in view
            $order->vendor_subtotal = $totals['subtotal'];
            $order->vendor_shipping = $totals['shipping'];
            $order->vendor_discount = $totals['discount'];
            $order->vendor_total = $totals['total'];
            $order->vendor_paid = $totals['paid'];
            $order->vendor_remaining = $totals['remaining'];
            $order->vendor_items_count = $totals['items_count'];
            $order->vendor_net_total = $totals['net_total'];
            $order->vendor_total_returned = $totals['total_returned'];
        }

        return view('dashboard.vendor.orders.index', compact('orders'));
    }

    public function show(EcommerceOrder $order)
    {
        $vendorId = auth('vendor')->id();

        // Ensure the order contains products from this vendor
        abort_unless($order->hasVendorItems($vendorId), 403);

        $order->load(['user', 'userAddress']);

        // Get only vendor's items with detailed relationships
        $vendorItems = $order->items()
            ->whereHas('product', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->with([
                'product.branch',
                'shipmentCompany',
                'pickupBranch',
                'variant',
                'returnRequestItems.returnRequest'
            ])
            ->get();

        // Use helper method for totals
        $vendorTotals = $order->getVendorTotals($vendorId);

        // Calculate per-item details including returns
        foreach ($vendorItems as $item) {
            // Calculate net values after returns
            $item->net_quantity = $item->quantity - ($item->returned_quantity ?? 0);
            $item->net_amount = $item->final_price - ($item->returned_amount ?? 0);
            $item->net_paid = $item->paid_amount - ($item->returned_amount ?? 0);
            $item->net_remaining = $item->remaining_amount;

            // Pickup/Dropoff info
            $item->pickup_location = $item->pickupBranch ?: optional($item->product)->branch;
            $item->dropoff_location = $order->userAddress;
        }

        $shipmentCompanies = ShipmentCompany::active()->get();
        $branches = VendorBranch::where('vendor_id', $vendorId)
            ->where('status', 1)
            ->get();

        // Get shipment service
        $shipmentService = app(\App\Services\ShipmentSuggestionService::class);

        // Compute eligible shipment companies with prices for each item
        foreach ($vendorItems as $item) {
            $pickup = $item->pickupBranch ?: optional($item->product)->branch;
            $address = $order->userAddress;

            $eligibleCompaniesWithPrices = collect();

            if ($pickup && $address) {
                foreach ($shipmentCompanies as $company) {
                    // Check if company covers both pickup and dropoff
                    $coversPickup = ShipmentLocation::active()
                        ->where('shipment_company_id', $company->id)
                        ->whereJsonContains('state', (string) $pickup->state_id)
                        ->whereJsonContains('city', (string) $pickup->city_id)
                        ->whereJsonContains('zone', (string) $pickup->zone_id)
                        ->exists();

                    if (!$coversPickup) continue;

                    $coversDrop = ShipmentLocation::active()
                        ->where('shipment_company_id', $company->id)
                        ->whereJsonContains('state', (string) $address->state_id)
                        ->whereJsonContains('city', (string) $address->city_id)
                        ->whereJsonContains('zone', (string) $address->zone_id)
                        ->exists();

                    if (!$coversDrop) continue;

                    // Calculate price for this company
                    $price = $this->calculateShipmentPriceForItem($item, $company, $pickup, $address);

                    $eligibleCompaniesWithPrices->push([
                        'id' => $company->id,
                        'name' => $company->name,
                        'logo' => $company->logo,
                        'price_per_km' => $company->price_per_km,
                        'est_days' => $company->est_days,
                        'shipment_price' => $price['shipment_price'],
                        'distance_km' => $price['distance_km'],
                        'client_total' => $price['client_total'],
                        'company_total' => $price['company_total'],
                        'is_current' => $item->shipment_company_id == $company->id
                    ]);
                }
            }

            $item->setAttribute('eligible_companies_with_prices', $eligibleCompaniesWithPrices);
            $item->setAttribute('eligible_companies', $eligibleCompaniesWithPrices); // Keep for backward compatibility
        }

        return view('dashboard.vendor.orders.show', compact(
            'order',
            'vendorItems',
            'vendorTotals',
            'shipmentCompanies',
            'branches'
        ));
    }

    /**
     * Calculate shipment price for a specific item and company
     */
    private function calculateShipmentPriceForItem($item, $company, $pickup, $address)
    {
        // Use Google Maps service to calculate distance
        $googleMapsService = app(\App\Services\GoogleMapsService::class);
        $shipmentService = app(\App\Services\ShipmentSuggestionService::class);

        // Calculate distance
        $distanceKm = $googleMapsService->distanceInKm(
            $pickup->latitude,
            $pickup->longitude,
            $address->latitude,
            $address->longitude
        );

        // Prepare package data similar to order creation
        $package = [
            'id' => $item->id,
            'category_id' => $item->product->category_id,
            'sub_category_id' => $item->product->sub_category_id,
            'weight' => $item->product->package_weight ?? 1,
            'size' => ($item->product->package_height ?? 1) *
                    ($item->product->package_length ?? 1) *
                    ($item->product->package_width ?? 1),
            'piece' => $item->quantity,
            'piece_type' => $item->product->piece_type ?? 'small',
            'pieces_per_package' => $item->product->pieces_per_package ?? 1,
        ];

        // Prepare pickup/dropoff data
        $pickupData = [
            'latitude' => $pickup->latitude,
            'longitude' => $pickup->longitude,
            'city_id' => $pickup->city_id,
            'state_id' => $pickup->state_id,
            'zone_id' => $pickup->zone_id,
            'city_name' => optional($pickup->city)->name_en,
            'is_village' => $pickup->is_village ?? false,
        ];

        $dropoffData = [
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
            'city_id' => $address->city_id,
            'state_id' => $address->state_id,
            'zone_id' => $address->zone_id,
            'city_name' => optional($address->city)->name_en,
            'is_village' => $address->is_village ?? false,
        ];

        // Use the shipment service to calculate price
        $result = $shipmentService->directPriceForCompany(
            $company->id,
            $package,
            $pickupData,
            $dropoffData
        );

        if ($result['covered']) {
            return [
                'shipment_price' => $result['price']['client_total'],
                'distance_km' => $result['price']['distance_km'],
                'client_total' => $result['price']['client_total'],
                'company_total' => $result['price']['company_total'],
            ];
        }

        // Fallback: calculate based on distance and price_per_km
        $shipmentPrice = round($distanceKm * $company->price_per_km, 2);

        return [
            'shipment_price' => $shipmentPrice,
            'distance_km' => $distanceKm,
            'client_total' => $shipmentPrice,
            'company_total' => $shipmentPrice,
        ];
    }

    public function updateStatus(Request $request, EcommerceOrder $order)
    {
        $vendorId = auth('vendor')->id();

        abort_unless($order->hasVendorItems($vendorId), 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_column(OrderStatus::cases(), 'value'))]
        ]);

        // Update only the items that belong to this vendor
        $order->items()
            ->whereHas('product', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->update(['status' => $validated['status']]);

        $this->updateOrderStatus($order);

        return redirect()
            ->back()
            ->with('success', __('vendor-dashboard.order_status_updated'));
    }

    public function acceptItem(Request $request, EcommerceOrder $order, EcommerceOrderItem $item)
    {
        $vendorId = auth('vendor')->id();

        abort_unless($item->ecommerce_order_id === $order->id, 404);
        abort_unless($item->product && $item->product->vendor_id === $vendorId, 403);

        $validated = $request->validate([
            'pickup_branch_id' => ['nullable', 'exists:vendor_branches,id'],
        ]);

        if (isset($validated['pickup_branch_id'])) {
            $branch = VendorBranch::where('id', $validated['pickup_branch_id'])
                ->where('vendor_id', $vendorId)
                ->first();

            abort_unless($branch, 403, 'Branch does not belong to your vendor account');
        }

        $pickupBranchId = $validated['pickup_branch_id']
            ?? optional($item->product->branch)->id
            ?? $item->pickup_branch_id;

        $item->update([
            'vendor_status' => 'accepted',
            'pickup_branch_id' => $pickupBranchId,
        ]);

        // $this->updateOrderStatus($order);

        return back()->with('success', __('vendor-dashboard.order_item_accepted'));
    }

    public function assignItemShipment(Request $request, EcommerceOrder $order, EcommerceOrderItem $item)
    {

        $vendorId = auth('vendor')->id();

        abort_unless($item->ecommerce_order_id === $order->id, 404);
        abort_unless($item->product && $item->product->vendor_id === $vendorId, 403);

        $validated = $request->validate([
            'shipment_company_id' => ['required','exists:shipment_companies,id'],
            'shipment_price_company' => ['required','numeric','min:0'],
            'distance' => ['nullable','numeric'],
            'est_days' => ['nullable','numeric'],
        ]);

        if ($item->vendor_status->value === 'pending' || $item->vendor_status->value === 'cancelled') {
            return back()->with('error', __('vendor-dashboard.please_accept_item_before_assigning_shipment'));
        }

        $shipmentCompany = ShipmentCompany::active()->findOrFail($validated['shipment_company_id']);

        $pickupBranch = $item->pickupBranch ?: optional($item->product)->branch;
        $userAddress = $order->userAddress;

        abort_unless($pickupBranch && $userAddress, 422, __('vendor-dashboard.missing_pickup_or_dropoff_location'));

        $hasPickupCoverage = ShipmentLocation::active()
            ->where('shipment_company_id', $shipmentCompany->id)
            ->whereJsonContains('state', (string) $pickupBranch->state_id)
            ->whereJsonContains('city', (string) $pickupBranch->city_id)
            ->whereJsonContains('zone', (string) $pickupBranch->zone_id)
            ->exists();

        $hasDropoffCoverage = ShipmentLocation::active()
            ->where('shipment_company_id', $shipmentCompany->id)
            ->whereJsonContains('state', (string) $userAddress->state_id)
            ->whereJsonContains('city', (string) $userAddress->city_id)
            ->whereJsonContains('zone', (string) $userAddress->zone_id)
            ->exists();

        abort_unless($hasPickupCoverage && $hasDropoffCoverage, 422, __('vendor-dashboard.shipment_company_does_not_cover_pickup_or_dropoff'));

        // Calculate distance and company-specific shipping price
        $googleMapsService = app(\App\Services\GoogleMapsService::class);
        $distanceKm = $googleMapsService->distanceInKm(
            $pickupBranch->latitude,
            $pickupBranch->longitude,
            $userAddress->latitude,
            $userAddress->longitude
        );

        $shipmentPriceCompany = round($distanceKm * $shipmentCompany->price_per_km, 2);

        $item->update([
            'shipment_company_id' => $validated['shipment_company_id'],
            'shipment_price_company' => $validated['shipment_price_company'],
            'distance' => $validated['distance'],
            'vendor_status' => 'shipped',
        ]);

        // $this->updateOrderStatus($order);

        return back()->with('success', __('vendor-dashboard.shipment_assigned_to_item_successfully_with_company_specific_pricing'));
    }

    public function cancelItem(Request $request, EcommerceOrder $order, EcommerceOrderItem $item)
    {
        $vendorId = auth('vendor')->id();

        abort_unless($item->ecommerce_order_id === $order->id, 404);
        abort_unless($item->product && $item->product->vendor_id === $vendorId, 403);

        abort_if(in_array($item->status, ['shipped', 'delivered']), 422, __('vendor-dashboard.cannot_cancel_after_shipment_started'));

        if ($order->payment_status === 'paid') {
            return back()->with('error', __('vendor-dashboard.cannot_cancel_paid_order'));
        }

        $validated = $request->validate([
            'cancellation_note' => 'required|string|min:3|max:500',
        ]);

        $item->update([
            'vendor_status' => 'cancelled',
            'shipment_company_id' => null,
            'cancellation_note' => $validated['cancellation_note'],
            'cancelled_at' => now(),
        ]);

        $this->updateOrderStatus($order);

        return back()->with('success', __('vendor-dashboard.item_cancelled_successfully'));
    }

    /**
     * Get vendor's financial summary for an order
     */
    public function getVendorOrderSummary(EcommerceOrder $order)
    {
        $vendorId = auth('vendor')->id();

        // Use the helper method
        $totals = $order->getVendorTotals($vendorId);
        $vendorItems = $order->vendorItems($vendorId);

        return [
            'order_number' => $order->order_number,
            'items_count' => $totals['items_count'],
            'subtotal' => $totals['subtotal'],
            'shipping_total' => $totals['shipping'],
            'discount_total' => $totals['discount'],
            'grand_total' => $totals['total'],
            'paid_amount' => $totals['paid'],
            'remaining_amount' => $totals['remaining'],
            'net_total' => $totals['net_total'],
            'total_returned' => $totals['total_returned'],
            'items' => $vendorItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'returned_quantity' => $item->returned_quantity ?? 0,
                    'net_quantity' => $item->quantity - ($item->returned_quantity ?? 0),
                    'unit_price' => $item->unit_price,
                    'product_total' => $item->total_price,
                    'shipping_cost' => $item->shipment_price,
                    'discount' => $item->discount_price ?? 0,
                    'product_discount' => $item->product_discount ?? 0,
                    'item_total' => $item->final_price,
                    'paid' => $item->paid_amount,
                    'remaining' => $item->remaining_amount,
                    'returned_amount' => $item->returned_amount ?? 0,
                    'net_amount' => $item->final_price - ($item->returned_amount ?? 0),
                    'distance_km' => $item->distance_km,
                    'status' => $item->status,
                ];
            }),
        ];
    }

    private function updateOrderStatus(EcommerceOrder $order)
    {
        $allItems = $order->items()->get();

        $highestPriority = 'pending';
        foreach (OrderStatus::cases() as $status) {
            if ($allItems->pluck('status')->contains($status->value)) {
                $highestPriority = $status->value;
                break;
            }
        }

        $order->update(['status' => $highestPriority]);
    }
}

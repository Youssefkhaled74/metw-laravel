<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\Product;
use App\Enum\OrderStatus;
use App\Enum\ReturnStatus;
use App\Enum\VendorOrderStatus;
use App\Models\ReturnRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $vendorId = auth('vendor')->id();
        $vendor = auth('vendor')->user();

        // Get counts for dashboard stats
        $totalProducts = Product::where('vendor_id', $vendorId)->count();
        $activeProducts = Product::where('vendor_id', $vendorId)->where('is_active', true)->count();
        $totalOrders = EcommerceOrder::whereHas('items.product', function($query) {
            $query->where('vendor_id', auth('vendor')->id());
        })->count();
        $pendingOrders = EcommerceOrder::whereHas('items.product', function($query) {
            $query->where('vendor_id', auth('vendor')->id());
        })->where('status', OrderStatus::PENDING->value)->count();

        // Urgent tasks: new/incomplete sales orders for this vendor
        $incompleteSalesOrders = EcommerceOrder::query()
            ->whereHas('items.product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->whereNotIn('status', [
                OrderStatus::DELIVERED->value,
                OrderStatus::CANCELLED->value,
                OrderStatus::RETURNED->value,
            ])
            ->with([
                'user',
                'items' => function ($query) use ($vendorId) {
                    $query->whereHas('product', function ($productQuery) use ($vendorId) {
                        $productQuery->where('vendor_id', $vendorId);
                    })->with('product');
                }
            ])
            ->latest()
            ->take(4)
            ->get();

        // Urgent tasks: new/incomplete return requests for this vendor
        $incompleteReturnRequests = ReturnRequest::query()
            ->whereHas('items.orderItem.product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->whereIn('status', [
                ReturnStatus::REQUESTED->value,
                ReturnStatus::APPROVED->value,
                ReturnStatus::PICKUP->value,
                ReturnStatus::PROCESSING->value,
            ])
            ->with(['user', 'order'])
            ->latest()
            ->take(4)
            ->get();

        // Urgent tasks: latest unread notifications
        $latestUnreadNotifications = $vendor->unreadNotifications
            ->sortByDesc('created_at')
            ->take(5);

        return view('dashboard.vendor.index', compact(
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'pendingOrders',
            'incompleteSalesOrders',
            'incompleteReturnRequests',
            'latestUnreadNotifications'
        ));
    }

    public function reports()
    {
        // Add reporting logic here
        return view('dashboard.vendor.reports');
    }
}

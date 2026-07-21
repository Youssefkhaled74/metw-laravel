<?php

namespace App\Services;

use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\ReturnRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enum\OrderStatus;
use App\Enum\VendorOrderStatus;
use App\Enum\ReturnStatus;
use App\Enum\CancellationSellerStatus;
use App\Enum\RequestType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class VendorUrgentTaskService
{
    protected int $vendorId;

    public function __construct(int $vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     * Get all urgent tasks grouped by type
     */
    public function getAllTasks(string $sortBy = 'priority', string $search = ''): array
    {
        $purchases = $this->getPurchaseOrders($search);
        $cancellations = $this->getCancellationRequests($search);
        $returns = $this->getReturnRequests($search);
        $shippedOrders = $this->getShippedOrders($search);
        $notifications = $this->getNotifications($search);

        $tasks = collect();
        $tasks = $tasks->concat($purchases);
        $tasks = $tasks->concat($cancellations);
        $tasks = $tasks->concat($returns);
        $tasks = $tasks->concat($shippedOrders);
        $tasks = $tasks->concat($notifications);

        // Sort
        $tasks = match ($sortBy) {
            'priority' => $tasks->sortByDesc('priority_order')->values(),
            'newest' => $tasks->sortByDesc('created_at')->values(),
            'oldest' => $tasks->sortBy('created_at')->values(),
            default => $tasks->sortByDesc('priority_order')->values(),
        };

        return [
            'tasks' => $tasks,
            'stats' => $this->getStats($purchases, $cancellations, $returns, $shippedOrders, $notifications),
        ];
    }

    /**
     * Get purchase orders needing vendor acceptance
     */
    public function getPurchaseOrders(string $search = ''): Collection
    {
        $query = EcommerceOrderItem::query()
            ->whereHas('product', fn($q) => $q->where('vendor_id', $this->vendorId))
            ->where('vendor_status', VendorOrderStatus::PENDING->value)
            ->whereHas('order', fn($q) => $q->where('status', OrderStatus::PENDING->value))
            ->with([
                'order' => fn($q) => $q->with('user'),
                'product',
            ]);

        if ($search) {
            $query->whereHas('order', fn($q) => $q->where('order_number', 'like', "%{$search}%"));
        }

        return $query->get()->map(function ($item) {
            $age = Carbon::parse($item->created_at);
            $hoursOld = $age->diffInHours(now());

            return [
                'type' => 'purchase',
                'type_label' => 'طلب شراء',
                'type_icon' => 'fas fa-shopping-cart',
                'type_color' => 'primary',
                'id' => $item->id,
                'order_number' => $item->order->order_number ?? '—',
                'customer_name' => $item->order->user->name ?? '—',
                'product_name' => $item->product->name ?? '—',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->quantity * $item->price,
                'created_at' => $item->created_at,
                'age_text' => $this->getAgeText($hoursOld),
                'priority' => $hoursOld > 12 ? 'high' : 'medium',
                'priority_label' => $hoursOld > 12 ? 'عالية' : 'متوسطة',
                'priority_order' => $hoursOld > 12 ? 2 : 3,
                'status_label' => 'بانتظار القبول',
                'status_color' => 'warning',
                'detail_url' => route('vendor.orders.show', $item->order_id),
                'process_url' => route('vendor.orders.update-status', $item->order_id),
            ];
        });
    }

    /**
     * Get cancellation requests needing vendor response
     */
    public function getCancellationRequests(string $search = ''): Collection
    {
        $query = ReturnRequest::query()
            ->where('request_type', RequestType::CANCELLATION->value)
            ->where('seller_status', null)
            ->where('status', ReturnStatus::REQUESTED->value)
            ->whereHas('items.orderItem.product', fn($q) => $q->where('vendor_id', $this->vendorId))
            ->with(['user', 'order']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->get()->map(function ($request) {
            $age = Carbon::parse($request->created_at);
            $hoursOld = $age->diffInHours(now());

            return [
                'type' => 'cancellation',
                'type_label' => 'طلب إلغاء',
                'type_icon' => 'fas fa-ban',
                'type_color' => 'danger',
                'id' => $request->id,
                'order_number' => $request->request_number ?? '—',
                'customer_name' => $request->user->name ?? '—',
                'reason' => $request->reason ?? '—',
                'created_at' => $request->created_at,
                'age_text' => $this->getAgeText($hoursOld),
                'priority' => $hoursOld > 24 ? 'critical' : 'high',
                'priority_label' => $hoursOld > 24 ? 'حرجة' : 'عالية',
                'priority_order' => $hoursOld > 24 ? 0 : 1,
                'status_label' => 'بانتظار رد البائع',
                'status_color' => 'danger',
                'detail_url' => route('vendor.return-requests.show', $request->id),
                'process_url' => route('vendor.return-requests.seller-status', $request->id),
            ];
        });
    }

    /**
     * Get return requests needing vendor response
     */
    public function getReturnRequests(string $search = ''): Collection
    {
        $query = ReturnRequest::query()
            ->where('request_type', RequestType::RETURN->value)
            ->where('seller_status', null)
            ->where('status', ReturnStatus::REQUESTED->value)
            ->whereHas('items.orderItem.product', fn($q) => $q->where('vendor_id', $this->vendorId))
            ->with(['user', 'order']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->get()->map(function ($request) {
            $age = Carbon::parse($request->created_at);
            $hoursOld = $age->diffInHours(now());

            return [
                'type' => 'return',
                'type_label' => 'طلب إرجاع',
                'type_icon' => 'fas fa-undo',
                'type_color' => 'warning',
                'id' => $request->id,
                'order_number' => $request->request_number ?? '—',
                'customer_name' => $request->user->name ?? '—',
                'reason' => $request->reason ?? '—',
                'created_at' => $request->created_at,
                'age_text' => $this->getAgeText($hoursOld),
                'priority' => $hoursOld > 24 ? 'critical' : 'high',
                'priority_label' => $hoursOld > 24 ? 'حرجة' : 'عالية',
                'priority_order' => $hoursOld > 24 ? 0 : 1,
                'status_label' => 'بانتظار رد البائع',
                'status_color' => 'warning',
                'detail_url' => route('vendor.return-requests.show', $request->id),
                'process_url' => route('vendor.return-requests.seller-status', $request->id),
            ];
        });
    }

    /**
     * Get shipped orders that need tracking updates or follow-up
     */
    public function getShippedOrders(string $search = ''): Collection
    {
        $query = OrderItem::query()
            ->where('vendor_id', $this->vendorId)
            ->where('status', OrderStatus::SHIPPED->value)
            ->whereHas('order', fn($q) => $q->where('shipment_company_id', '!=', null))
            ->with([
                'order' => fn($q) => $q->with('user'),
                'product',
            ]);

        if ($search) {
            $query->whereHas('order', fn($q) => $q->where('order_number', 'like', "%{$search}%"));
        }

        return $query->get()->map(function ($item) {
            $age = Carbon::parse($item->updated_at);
            $hoursOld = $age->diffInHours(now());

            return [
                'type' => 'shipping',
                'type_label' => 'شحن',
                'type_icon' => 'fas fa-truck',
                'type_color' => 'info',
                'id' => $item->id,
                'order_number' => $item->order->order_number ?? '—',
                'customer_name' => $item->order->user->name ?? '—',
                'product_name' => $item->product->name ?? '—',
                'quantity' => $item->quantity,
                'created_at' => $item->updated_at,
                'age_text' => $this->getAgeText($hoursOld),
                'priority' => $hoursOld > 48 ? 'medium' : 'low',
                'priority_label' => $hoursOld > 48 ? 'متوسطة' : 'منخفضة',
                'priority_order' => $hoursOld > 48 ? 3 : 4,
                'status_label' => 'تم الشحن',
                'status_color' => 'info',
                'detail_url' => route('vendor.orders.show', $item->order_id),
                'process_url' => null,
            ];
        });
    }

    /**
     * Get unread vendor notifications
     */
    public function getNotifications(string $search = ''): Collection
    {
        $vendor = auth('vendor')->user();
        $notifications = $vendor->unreadNotifications()->latest()->get();

        if ($search) {
            $notifications = $notifications->filter(fn($n) =>
                str_contains($n->data['title'] ?? '', $search) ||
                str_contains($n->data['message'] ?? '', $search)
            )->values();
        }

        return $notifications->map(function ($notification) {
            $age = Carbon::parse($notification->created_at);
            $hoursOld = $age->diffInHours(now());

            return [
                'type' => 'notification',
                'type_label' => 'إشعار',
                'type_icon' => 'fas fa-bell',
                'type_color' => 'warning',
                'id' => $notification->id,
                'order_number' => $notification->data['title'] ?? 'إشعار جديد',
                'customer_name' => $notification->data['message'] ?? '',
                'created_at' => $notification->created_at,
                'age_text' => $this->getAgeText($hoursOld),
                'priority' => 'medium',
                'priority_label' => 'متوسطة',
                'priority_order' => 3,
                'status_label' => 'جديد',
                'status_color' => 'warning',
                'detail_url' => $notification->data['url'] ?? null,
                'process_url' => null,
                'notification_id' => $notification->id,
            ];
        });
    }

    /**
     * Get aggregated stats
     */
    public function getStats(
        ?Collection $purchases = null,
        ?Collection $cancellations = null,
        ?Collection $returns = null,
        ?Collection $shippedOrders = null,
        ?Collection $notifications = null
    ): array {
        $purchases ??= $this->getPurchaseOrders();
        $cancellations ??= $this->getCancellationRequests();
        $returns ??= $this->getReturnRequests();
        $shippedOrders ??= $this->getShippedOrders();
        $notifications ??= $this->getNotifications();

        $allTasks = $purchases->concat($cancellations)->concat($returns)->concat($shippedOrders)->concat($notifications);
        $criticalCount = $allTasks->where('priority', 'critical')->count();
        $highCount = $allTasks->where('priority', 'high')->count();

        return [
            'total' => $allTasks->count(),
            'critical' => $criticalCount,
            'high' => $highCount,
            'purchases' => $purchases->count(),
            'cancellations' => $cancellations->count(),
            'returns' => $returns->count(),
            'shipping' => $shippedOrders->count(),
            'notifications' => $notifications->count(),
        ];
    }

    /**
     * Process a single task (mark as handled)
     */
    public function processTask(string $type, int $id): array
    {
        return match ($type) {
            'purchase' => $this->processPurchase($id),
            'cancellation' => $this->processCancellationRequest($id),
            'return' => $this->processReturnRequest($id),
            'shipping' => $this->processShipping($id),
            default => ['success' => false, 'message' => 'نوع المهمة غير معروف'],
        };
    }

    protected function processPurchase(int $id): array
    {
        $item = EcommerceOrderItem::where('id', $id)
            ->whereHas('product', fn($q) => $q->where('vendor_id', $this->vendorId))
            ->first();

        if (!$item) {
            return ['success' => false, 'message' => 'العنصر غير موجود أو ليس تابعاً لvendor'];
        }

        $item->vendor_status = VendorOrderStatus::ACCEPTED->value;
        $item->save();

        return ['success' => true, 'message' => 'تم قبول الطلب بنجاح'];
    }

    protected function processCancellationRequest(int $id): array
    {
        $request = ReturnRequest::where('id', $id)
            ->whereHas('items.orderItem.product', fn($q) => $q->where('vendor_id', $this->vendorId))
            ->first();

        if (!$request) {
            return ['success' => false, 'message' => 'طلب الإلغاء غير موجود أو ليس تابعاً لvendor'];
        }

        $request->seller_status = CancellationSellerStatus::APPROVED->value;
        $request->save();

        return ['success' => true, 'message' => 'تم الموافقة على طلب الإلغاء'];
    }

    protected function processReturnRequest(int $id): array
    {
        $request = ReturnRequest::where('id', $id)
            ->whereHas('items.orderItem.product', fn($q) => $q->where('vendor_id', $this->vendorId))
            ->first();

        if (!$request) {
            return ['success' => false, 'message' => 'طلب الإرجاع غير موجود أو ليس تابعاً لvendor'];
        }

        $request->seller_status = CancellationSellerStatus::RETURN_ACCEPTED->value;
        $request->save();

        return ['success' => true, 'message' => 'تم الموافقة على طلب الإرجاع'];
    }

    protected function processShipping(int $id): array
    {
        $item = OrderItem::where('id', $id)
            ->where('vendor_id', $this->vendorId)
            ->first();

        if (!$item) {
            return ['success' => false, 'message' => 'العنصر غير موجود أو ليس تابعاً لvendor'];
        }

        $item->status = OrderStatus::DELIVERED->value;
        $item->save();

        return ['success' => true, 'message' => 'تم تأكيد التسليم'];
    }

    /**
     * Convert hours to human-readable age text in Arabic
     */
    protected function getAgeText(float $hours): string
    {
        if ($hours < 1) {
            return 'منذ دقائق';
        } elseif ($hours < 24) {
            $h = floor($hours);
            return "منذ {$h} " . ($h === 1 ? 'ساعة' : 'ساعات');
        } else {
            $days = floor($hours / 24);
            return "منذ {$days} " . ($days === 1 ? 'يوم' : 'أيام');
        }
    }
}

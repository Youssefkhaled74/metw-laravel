<?php

namespace App\Models;

use App\Enum\PaymentStatus;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ContactAdmin;
class EcommerceOrder extends Model
{
    use HasFactory, SoftDeletes , GeneratesPrefixedNumber;

    protected $fillable = [
        'user_id',
        'user_address_id',
        'cart_id',
        'status',
        'order_number',
        'tracking_number',
        'subtotal',
        'shipping_price',
        'discount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'phone',
        'ecommerce_cart_id',
        'actual_delivery_date',
        'estimated_delivery_to',
        'estimated_delivery_from',
        'promo_code_id',
        'shipment_company_id',
        'warehouse_id',
        'payment_status',
        'final_price',
        'delivered_at',
        'delivery_otp',
        'otp_verified',
    ];

    protected $casts = [
        'actual_delivery_date' => 'date',
        'estimated_delivery_to' => 'date',
        'estimated_delivery_from' => 'date',
        'payment_status' => PaymentStatus::class,
        'otp_verified' => 'boolean',
    ];

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('order_number', 'ORD');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class);
    }

    public function cart()
    {
        return $this->belongsTo(EcommerceCart::class);
    }

    public function items()
    {
        return $this->hasMany(EcommerceOrderItem::class)
                    ->with(['product' => function ($query) {
                        $query->withoutGlobalScope('active');
                    }, 'variant']);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function returns()
    {
        return $this->hasMany(EcommerceOrderReturn::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get vendor-specific items for this order
     */
    public function vendorItems($vendorId)
    {
        return $this->items()
            ->whereHas('product', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->get();
    }

    /**
     * Calculate totals for a specific vendor's items only
     *
     * @param int $vendorId
     * @return array
     */
    public function getVendorTotals($vendorId)
    {
        $vendorItems = $this->vendorItems($vendorId);

        return [
            'items_count' => $vendorItems->count(),
            'subtotal' => $vendorItems->sum('total_price'),
            'shipping' => $vendorItems->sum('shipment_price'),
            'discount' => $vendorItems->sum('discount_price'),
            'product_discount' => $vendorItems->sum('product_discount'),
            'total' => $vendorItems->sum('final_price'),
            'paid' => $vendorItems->sum('paid_amount'),
            'remaining' => $vendorItems->sum('remaining_amount'),

            // Net amounts after returns
            'net_total' => $vendorItems->sum(function($item) {
                return $item->final_price - ($item->returned_amount ?? 0);
            }),
            'net_paid' => $vendorItems->sum(function($item) {
                return $item->paid_amount - ($item->returned_amount ?? 0);
            }),
            'total_returned' => $vendorItems->sum('returned_amount'),
        ];
    }

    /**
     * Check if this order has items from a specific vendor
     */
    public function hasVendorItems($vendorId)
    {
        return $this->items()
            ->whereHas('product', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->exists();
    }

    /**
     * Get all vendors that have items in this order
     */
    public function getVendorsAttribute()
    {
        return $this->items()
            ->with('product.vendor')
            ->get()
            ->pluck('product.vendor')
            ->filter()
            ->unique('id');
    }

    /**
     * Get shipment company-specific items for this order
     */
    public function shipmentCompanyItems($shipmentCompanyId)
    {
        return $this->items()
            ->where('shipment_company_id', $shipmentCompanyId)
            ->get();
    }

    /**
     * Calculate totals for a specific shipment company's items only
     *
     * @param int $shipmentCompanyId
     * @return array
     */
    public function getShipmentCompanyTotals($shipmentCompanyId)
    {
        $companyItems = $this->shipmentCompanyItems($shipmentCompanyId);

        return [
            'items_count' => $companyItems->count(),
            'subtotal' => $companyItems->sum('total_price'),
            'shipping' => $companyItems->sum(function($item) {
                // Use shipment_price_company if available, otherwise fall back to shipment_price
                return $item->shipment_price_company > 0
                    ? $item->shipment_price_company
                    : $item->shipment_price;
            }),
            'discount' => $companyItems->sum('discount_price'),
            'product_discount' => $companyItems->sum('product_discount'),
            'total' => $companyItems->sum('final_price'),
            'paid' => $companyItems->sum('paid_amount'),
            'remaining' => $companyItems->sum('remaining_amount'),
            'distance' => $companyItems->sum('distance'),

            // Net amounts after returns
            'net_total' => $companyItems->sum(function($item) {
                return $item->final_price - ($item->returned_amount ?? 0);
            }),
            'net_paid' => $companyItems->sum(function($item) {
                return $item->paid_amount - ($item->returned_amount ?? 0);
            }),
            'total_returned' => $companyItems->sum('returned_amount'),
        ];
    }

    /**
     * Check if this order has items assigned to a specific shipment company
     */
    public function hasShipmentCompanyItems($shipmentCompanyId)
    {
        return $this->items()
            ->where('shipment_company_id', $shipmentCompanyId)
            ->exists();
    }

    public function updateOrderPaymentTotals()
    {
        $totalPaid = $this->items->sum('paid_amount');
        $this->paid_amount = $totalPaid;
        $this->remaining_amount = max(0, $this->total_amount - $totalPaid);
        $this->save();
    }

    public function paymentRecords()
    {
        return $this->hasMany(OrderPaymentRecord::class);
    }

    public function canAcceptOrderPayment()
    {
        // Order can accept payment if at least one item is ready for payment
        return $this->items->contains(function ($item) {
            return $item->canAcceptPayment();
        });
    }

    public function hasUnpaidDepositItems(): bool
    {
        return $this->items->contains(function ($item) {
            return $item->product
                && $item->product->has_deposit
                && ! $item->depositIsPaid();
        });
    }

    public function buildWhatsappMessage(): string
    {
        $this->loadMissing('user', 'items.product');

        $username = $this->user->username ?? 'عميل';
        $date = $this->created_at?->format('Y/m/d') ?? now()->format('Y/m/d');
        $time = $this->created_at?->format('h:i A') ?? now()->format('h:i A');

        $depositPercent = 0;
        foreach ($this->items as $item) {
            if ($item->product?->has_deposit) {
                $depositPercent = (float) ($item->product->deposit_percentage ?? 0);
                break;
            }
        }

        $template = $this->resolveWhatsappTemplateByStatus((string) $this->status);

        return $this->replaceWhatsappPlaceholders($template, [
            '{customer_name}' => $username,
            '{phone}' => (string) ($this->phone ?? ''),
            '{order_number}' => (string) ($this->order_number ?? ''),
            '{order_date}' => $date,
            '{order_time}' => $time,
            '{total_amount}' => number_format((float) ($this->total_amount ?? 0), 2),
            '{paid_amount}' => number_format((float) ($this->paid_amount ?? 0), 2),
            '{remaining_amount}' => number_format((float) ($this->remaining_amount ?? 0), 2),
            '{deposit_percent}' => (string) $depositPercent,
            '{payments_accounts}' => $this->buildPaymentsText(),
        ]);
    }

    private function resolveWhatsappTemplateByStatus(string $status): string
    {
        $allowedStatuses = WhatsappTemplate::fixedKeys();
        $statusKey = in_array($status, $allowedStatuses, true) ? $status : 'pending';

        try {
            $content = WhatsappTemplate::query()
                ->where('key', $statusKey)
                ->where('is_active', true)
                ->value('content');
        } catch (\Throwable $e) {
            $content = null;
        }

        if (!is_string($content) || trim($content) === '') {
            return WhatsappTemplate::defaultContent($statusKey);
        }

        return $content;
    }

    private function buildPaymentsText(): string
    {
        $contacts = ContactAdmin::active()->get();
        if ($contacts->isEmpty()) {
            return 'لا توجد وسائل دفع متاحة حالياً';
        }

        $lines = [];
        foreach ($contacts as $contact) {
            $lines[] = trim(($contact->name ?? '') . ' ' . ($contact->value ?? ''));
        }

        return implode("\n", array_filter($lines));
    }

    private function replaceWhatsappPlaceholders(string $template, array $placeholders): string
    {
        return strtr($template, $placeholders);
    }

    public function getWhatsappUrlAttribute()
    {
        $this->loadMissing('user','items.product');

        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (str_starts_with($phone, '01')) {
            $phone = '20' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '20')) {
            $phone = '20' . ltrim($phone, '0');
        }

        $message = $this->buildWhatsappMessage();

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }



}

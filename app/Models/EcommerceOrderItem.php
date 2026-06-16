<?php

namespace App\Models;

use App\Enum\OrderStatus;
use App\Enum\ReturnStatus;
use App\Enum\VendorOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcommerceOrderItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'ecommerce_order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'pickup_branch_id',
        'shipment_company_id',
        'shipment_price',
        'shipment_price_company',
        'distance',
        'final_price',
        'paid_amount',
        'remaining_amount',
        'product_discount',
        'discount_price',
        'vendor_status',
        'delivered_at',
        'is_shipment_accepted',
        'cancellation_note',
        'cancelled_at',
    ];
    protected $casts = [
        'status' => OrderStatus::class,
        'vendor_status' => VendorOrderStatus::class,
        'cancelled_at' => 'datetime',
    ];
    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'ecommerce_order_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function returnRequestItems()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    public function pickupBranch()
    {
        return $this->belongsTo(VendorBranch::class, 'pickup_branch_id');
    }

    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class, 'shipment_company_id');
    }
        // helper method عشان تجيب الكمية المرتجعة
   public function getReturnedQuantityAttribute()
    {
        return $this->returnRequestItems()
            ->whereHas('returnRequest', function ($q) {
                $q->whereIn('status', [
                    ReturnStatus::APPROVED->value,
                    ReturnStatus::PICKUP->value,
                    ReturnStatus::PROCESSING->value,
                    ReturnStatus::REFUNDED->value
                ]);
            })
            ->sum('return_quantity');
    }

    // helper method عشان تجيب المبلغ المرتجع
    public function getReturnedAmountAttribute()
    {
        return $this->returnRequestItems()
            ->whereHas('returnRequest', function ($q) {
                $q->whereIn('status', [
                    ReturnStatus::APPROVED->value,
                    ReturnStatus::PICKUP->value,
                    ReturnStatus::PROCESSING->value,
                    ReturnStatus::REFUNDED->value
                ]);
            })
            ->sum('return_price');
    }

    public function getIsReturnableAttribute(): bool
    {
        // 1. لازم المنتج يكون قابل للإرجاع
        if (!$this->product?->is_returnable) {
            return false;
        }

        // 2. لازم يكون العنصر ده متسلم فعلاً
        $deliveredAt = $this->order->delivered_at; // خاص بكل item
        if (!$deliveredAt) {
            return false;
        }

        // 3. لازم المنتج يكون له فترة صلاحية للإرجاع
        $validityDays = $this->product->return_validity ?? 0;
        if ($validityDays <= 0) {
            return false;
        }

        // 4. نحسب عدد الأيام اللي عدت من تاريخ التسليم
        $daysPassed = now()->diffInDays($deliveredAt);

        // 5. المنتج قابل للإرجاع لو المدة ما انتهتش
        return $daysPassed <= $validityDays;
    }

    public function paymentRecords()
    {
        return $this->hasMany(OrderPaymentRecord::class);
    }

    public function updatePaymentAmounts()
    {
        $totalPaid = $this->paymentRecords()->sum('amount');
        $this->paid_amount = $totalPaid;
        $this->remaining_amount = max(0, $this->final_price - $totalPaid);
        $this->save();
    }

    public function canAcceptPayment()
    {
        // Can accept payment if:
        // 1. Vendor has accepted the order
        // 2. Shipment company is assigned
        return $this->vendor_status === \App\Enum\VendorOrderStatus::SHIPPED
            && $this->shipment_company_id !== null;
    }

    public function getDepositAmount()
    {
        if ($this->product->has_deposit && $this->product->deposit_percentage > 0) {
            return ($this->final_price * $this->product->deposit_percentage) / 100;
        }
        return 0;
    }

    public function depositIsPaid(): bool
    {
        if (! $this->product || ! $this->product->has_deposit) {
            return true; // no deposit required
        }

        return $this->paid_amount >= $this->getDepositAmount();
    }


}

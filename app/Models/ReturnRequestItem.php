<?php

namespace App\Models;

use App\Enum\ReturnStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'ecommerce_order_item_id',
        'return_quantity',
        'return_price',
        'return_reason',
        'status',

        // ===== إضافات مالية =====
        'item_subtotal',             // إجمالي سعر العنصر قبل الخصم
        'vendor_id',                 // الفيندور
        'vendor_refund_commission',  // عمولة المرتجع على الفيندور
        'shipment_company_id',       // شركة الشحن
        'return_shipping_cost',      // تكلفة الشحن المرتجع
        'shipment_commission',       // عمولة الشحن
        'shipment_net',              // صافي الشحن للشركة
        'customer_refund_amount',    // صافي المبلغ اللي هيرجع للعميل
    ];
    protected $casts = [
        'status' => ReturnStatus::class,
    ];
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(EcommerceOrderItem::class, 'ecommerce_order_item_id');
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            EcommerceOrderItem::class,
            'id',
            'id',
            'ecommerce_order_item_id',
            'product_id'
        );
    }
}

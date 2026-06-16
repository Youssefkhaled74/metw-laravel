<?php

namespace App\Models;

use App\Enum\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageTracking extends Model
{
    use HasFactory;

    protected $table = 'package_tracking';

    protected $fillable = [
        'package_id',
        'order_item_id',
        'status',
        'location',
        'description',
        'occurred_at',
        'metadata',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata'    => 'array',
        'status'      => OrderStatus::class,
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}

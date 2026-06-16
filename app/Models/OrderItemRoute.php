<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemRoute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'from_address',
        'from_latitude',
        'from_longitude',
        'from_city_id',
        'from_state_id',
        'from_zone_id',
        'to_address',
        'to_latitude',
        'to_longitude',
        'to_city_id',
        'to_state_id',
        'to_zone_id',
        'leg_type',
        'leg_order',
        'distance',
        'cost',
        'pickup_company_id',
        'dropoff_company_id',
    ];

    protected $casts = [
        'from_address' => 'array',
        'to_address' => 'array',
        'from_latitude' => 'decimal:8',
        'from_longitude' => 'decimal:8',
        'to_latitude' => 'decimal:8',
        'to_longitude' => 'decimal:8',
        'distance' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    /**
     * Order item relationship
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * From city
     */
    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * From state
     */
    public function fromState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'from_state_id');
    }

    /**
     * From zone
     */
    public function fromZone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'from_zone_id');
    }

    /**
     * To city
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * To state
     */
    public function toState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'to_state_id');
    }

    /**
     * To zone
     */
    public function toZone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'to_zone_id');
    }

    /**
     * Check if this is a pickup leg
     */
    public function isPickupLeg(): bool
    {
        return $this->leg_type === 'pickup';
    }

    /**
     * Check if this is a dropoff leg
     */
    public function isDropoffLeg(): bool
    {
        return $this->leg_type === 'dropoff';
    }

    /**
     * Check if this is a direct delivery
     */
    public function isDirect(): bool
    {
        return $this->leg_type === 'direct';
    }

    /**
     * Get formatted from address
     */
    public function getFromAddressTextAttribute(): string
    {
        if (is_array($this->from_address)) {
            return $this->from_address['address'] ??
                $this->from_address['location'] ??
                'Unknown';
        }
        return 'Unknown';
    }

    /**
     * Get formatted to address
     */
    public function getToAddressTextAttribute(): string
    {
        if (is_array($this->to_address)) {
            return $this->to_address['address'] ??
                $this->to_address['location'] ??
                'Unknown';
        }
        return 'Unknown';
    }
}

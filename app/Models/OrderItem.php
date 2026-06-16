<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'order_id',
        'package_id',
        'shipment_company_id',
        'est_date',
        'est_price',
        'item_number',
        'status',
        'parent_id',
        'is_split'
    ];
    protected $casts = [
        'est_date' => 'date',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class);
    }
    public function trackings()
    {
        return $this->hasMany(PackageTracking::class);
    }
    public static function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    public function parent()
    {
        return $this->belongsTo(OrderItem::class, 'parent_id');
    }

    /**
     * Child order items (legs of split delivery)
     */
    public function childItems()
    {
        return $this->hasMany(OrderItem::class, 'parent_id');
    }

    /**
     * Route details
     */
    public function route()
    {
        return $this->hasOne(OrderItemRoute::class);
    }

    /**
     * Get pickup address text (prefers route.from then package.pickupAddress)
     */
    public function getPickupAddressTextAttribute(): ?string
    {
        if ($this->relationLoaded('route') && $this->route && is_array($this->route->from_address)) {
            return $this->route->from_address['address'] ?? ($this->route->from_address['location'] ?? null);
        }
        if ($this->relationLoaded('package') && $this->package && $this->package->pickupAddress) {
            return $this->package->pickupAddress->address ?? ($this->package->pickupAddress->location ?? null);
        }
        return null;
    }

    /**
     * Get dropoff address text (prefers route.to then package.dropoffAddress)
     */
    public function getDropoffAddressTextAttribute(): ?string
    {
        if ($this->relationLoaded('route') && $this->route && is_array($this->route->to_address)) {
            return $this->route->to_address['address'] ?? ($this->route->to_address['location'] ?? null);
        }
        if ($this->relationLoaded('package') && $this->package && $this->package->dropoffAddress) {
            return $this->package->dropoffAddress->address ?? ($this->package->dropoffAddress->location ?? null);
        }
        return null;
    }

    /**
     * Coordinates and distance helpers (prefer route; fallback to package addresses)
     */
    public function getPickupLatitudeAttribute(): ?float
    {
        if ($this->relationLoaded('route') && $this->route && !is_null($this->route->from_latitude)) {
            return (float) $this->route->from_latitude;
        }
        if ($this->relationLoaded('package') && $this->package && $this->package->pickupAddress && !is_null($this->package->pickupAddress->latitude)) {
            return (float) $this->package->pickupAddress->latitude;
        }
        return null;
    }

    public function getPickupLongitudeAttribute(): ?float
    {
        if ($this->relationLoaded('route') && $this->route && !is_null($this->route->from_longitude)) {
            return (float) $this->route->from_longitude;
        }
        if ($this->relationLoaded('package') && $this->package && $this->package->pickupAddress && !is_null($this->package->pickupAddress->longitude)) {
            return (float) $this->package->pickupAddress->longitude;
        }
        return null;
    }

    public function getDropoffLatitudeAttribute(): ?float
    {
        if ($this->relationLoaded('route') && $this->route && !is_null($this->route->to_latitude)) {
            return (float) $this->route->to_latitude;
        }
        if ($this->relationLoaded('package') && $this->package && $this->package->dropoffAddress && !is_null($this->package->dropoffAddress->latitude)) {
            return (float) $this->package->dropoffAddress->latitude;
        }
        return null;
    }

    public function getDropoffLongitudeAttribute(): ?float
    {
        if ($this->relationLoaded('route') && $this->route && !is_null($this->route->to_longitude)) {
            return (float) $this->route->to_longitude;
        }
        if ($this->relationLoaded('package') && $this->package && $this->package->dropoffAddress && !is_null($this->package->dropoffAddress->longitude)) {
            return (float) $this->package->dropoffAddress->longitude;
        }
        return null;
    }

    public function getDistanceKmAttribute(): ?float
    {
        if ($this->relationLoaded('route') && $this->route && !is_null($this->route->distance)) {
            return (float) $this->route->distance;
        }
        return null;
    }

    public function getRouteCostAttribute(): ?float
    {
        if ($this->relationLoaded('route') && $this->route && !is_null($this->route->cost)) {
            return (float) $this->route->cost;
        }
        return $this->est_price !== null ? (float) $this->est_price : null;
    }

    /**
     * Pickup/Dropoff contact info from PackageDetails
     */
    public function getPickupContactNameAttribute(): ?string
    {
        if ($this->relationLoaded('package') && $this->package && $this->package->packageDetails) {
            return $this->package->packageDetails->sender_name ?? null;
        }
        return null;
    }

    public function getPickupContactPhoneAttribute(): ?string
    {
        if ($this->relationLoaded('package') && $this->package && $this->package->packageDetails) {
            return $this->package->packageDetails->sender_phone ?? null;
        }
        return null;
    }

    public function getDropoffContactNameAttribute(): ?string
    {
        if ($this->relationLoaded('package') && $this->package && $this->package->packageDetails) {
            return $this->package->packageDetails->recive_name ?? null;
        }
        return null;
    }

    public function getDropoffContactPhoneAttribute(): ?string
    {
        if ($this->relationLoaded('package') && $this->package && $this->package->packageDetails) {
            return $this->package->packageDetails->recive_phone ?? null;
        }
        return null;
    }

    /**
     * In split case, expose handoff companies' phones if available on model (assumes ShipmentCompany has phone)
     */
    public function getHandoffPickupCompanyPhoneAttribute(): ?string
    {
        if ($this->is_split && $this->relationLoaded('parent') && $this->parent && $this->parent->relationLoaded('pickupLeg')) {
            return $this->parent->pickupLeg?->shipmentCompany?->phone ?? null;
        }
        return null;
    }

    public function getHandoffDropoffCompanyPhoneAttribute(): ?string
    {
        if ($this->is_split && $this->relationLoaded('parent') && $this->parent && $this->parent->relationLoaded('dropoffLeg')) {
            return $this->parent->dropoffLeg?->shipmentCompany?->phone ?? null;
        }
        return null;
    }

    /**
     * Package tracking
     */
    public function tracking()
    {
        return $this->hasMany(PackageTracking::class);
    }

    /**
     * Check if this is a parent item
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id) && $this->is_split;
    }

    /**
     * Check if this is a child item (leg)
     */
    public function isChild(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get pickup leg (first child)
     */
    public function pickupLeg()
    {
        return $this->hasOne(OrderItem::class, 'parent_id')
            ->whereHas('route', function ($q) {
                $q->where('leg_type', 'pickup');
            })
            ->with('route');
    }

    /**
     * Get dropoff leg (second child)
     */
    public function dropoffLeg()
    {
        return $this->hasOne(OrderItem::class, 'parent_id')
            ->whereHas('route', function ($q) {
                $q->where('leg_type', 'dropoff');
            })
            ->with('route');
    }

    /**
     * Scope: Get only parent items
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id')->where('is_split', true);
    }

    /**
     * Scope: Get only child items (legs)
     */
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope: Get items for specific shipment company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('shipment_company_id', $companyId);
    }

    /**
     * Scope: Get direct deliveries (no split)
     */
    public function scopeDirect($query)
    {
        return $query->where('is_split', false);
    }
}

<?php

namespace App\Services;

use App\Enum\DiscountType;
use App\Models\Payment;
use App\Models\PromoCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromoCodeService
{
    /**
     * Validate and apply promo code
     */
    public function validateAndApply(string $code, float $subtotal, ?int $userId = null): array
    {
        $promoCode = PromoCode::where('code', $code)
            ->active()
            ->first();

        if (!$promoCode) {
            return [
                'valid' => false,
                'message' => 'Invalid promo code',
                'discount' => 0,
                'promo_code' => null
            ];
        }

        // Check if promo code is within validity period
        $now = Carbon::now();
        if ($promoCode->valid_from && $now->isBefore($promoCode->valid_from)) {
            return [
                'valid' => false,
                'message' => 'Promo code is not yet active',
                'discount' => 0,
                'promo_code' => null
            ];
        }

        if ($promoCode->valid_to && $now->isAfter($promoCode->valid_to)) {
            return [
                'valid' => false,
                'message' => 'Promo code has expired',
                'discount' => 0,
                'promo_code' => null
            ];
        }

        // Check maximum uses
        if ($promoCode->max_uses && $promoCode->uses >= $promoCode->max_uses) {
            return [
                'valid' => false,
                'message' => 'Promo code usage limit exceeded',
                'discount' => 0,
                'promo_code' => null
            ];
        }

        // Check user-specific usage limit
        if ($userId && $promoCode->user_max_uses) {
            $userUsageCount = Payment::where('promo_code_id', $promoCode->id)
                ->whereHas('order', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->count();

            if ($userUsageCount >= $promoCode->user_max_uses) {
                return [
                    'valid' => false,
                    'message' => 'You have reached the maximum usage limit for this promo code',
                    'discount' => 0,
                    'promo_code' => null
                ];
            }
        }

        // Calculate discount
        $discount = $this->calculateDiscount($promoCode, $subtotal);

        return [
            'valid' => true,
            'message' => 'Promo code applied successfully',
            'discount' => $discount,
            'promo_code' => $promoCode
        ];
    }

    /**
     * Calculate discount amount based on promo code type
     */
    private function calculateDiscount(PromoCode $promoCode, float $subtotal): float
    {
        if ($promoCode->discount_type === DiscountType::FIXED) {
            return min($promoCode->discount_value, $subtotal);
        }

        if ($promoCode->discount_type === DiscountType::PERCENTAGE) {
            return ($subtotal * $promoCode->discount_value) / 100;
        }

        return 0;
    }

    /**
     * Record promo code usage
     */
    public function recordUsage(PromoCode $promoCode): void
    {
        $promoCode->increment('uses');
    }

    /**
     * Get promo code by code
     */
    public function getByCode(string $code): ?PromoCode
    {
        return PromoCode::where('code', $code)->active()->first();
    }
}

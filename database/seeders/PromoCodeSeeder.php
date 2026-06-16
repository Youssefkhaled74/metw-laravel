<?php

namespace Database\Seeders;

use App\Enum\DiscountType;
use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'WELCOME10',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 10.00,
                'valid_from' => Carbon::now()->subDays(30),
                'valid_to' => Carbon::now()->addDays(30),
                'max_uses' => 1000,
                'user_max_uses' => 1,
                'uses' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SAVE50',
                'discount_type' => DiscountType::FIXED,
                'discount_value' => 50.00,
                'valid_from' => Carbon::now()->subDays(7),
                'valid_to' => Carbon::now()->addDays(7),
                'max_uses' => 100,
                'user_max_uses' => 2,
                'uses' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'FIRSTORDER',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 15.00,
                'valid_from' => Carbon::now()->subDays(15),
                'valid_to' => Carbon::now()->addDays(15),
                'max_uses' => 500,
                'user_max_uses' => 1,
                'uses' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'EXPIRED',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 20.00,
                'valid_from' => Carbon::now()->subDays(30),
                'valid_to' => Carbon::now()->subDays(1), // Expired
                'max_uses' => 50,
                'user_max_uses' => 1,
                'uses' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::create($promoCode);
        }
    }
}

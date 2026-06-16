<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['key' => 'shipment_vat', 'value' => '14', 'group' => 'shipment', 'is_active' => true],
            ['key' => 'max_package_weight', 'value' => '50', 'group' => 'shipment', 'is_active' => true],
            ['key' => 'max_package_dimensions', 'value' => '200x200x200', 'group' => 'shipment', 'is_active' => true],
            ['key' => 'auto_accept_orders', 'value' => '1', 'group' => 'ecommerce', 'is_active' => true],
            ['key' => 'vendor_approval_required', 'value' => '1', 'group' => 'vendor', 'is_active' => true],
            ['key' => 'max_cart_items', 'value' => '50', 'group' => 'ecommerce', 'is_active' => true],
            ['key' => 'otp_attempts_limit', 'value' => '5', 'group' => 'auth', 'is_active' => true],
            ['key' => 'otp_ban_minutes', 'value' => '30', 'group' => 'auth', 'is_active' => true],
        ];

        foreach ($configs as $config) {
            Config::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}

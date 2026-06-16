<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'Lasco Market'],
            ['key' => 'app_name_ar', 'value' => 'لاسكو ماركت'],
            ['key' => 'app_email', 'value' => 'info@lasco.test'],
            ['key' => 'app_phone', 'value' => '+201000000000'],
            ['key' => 'app_address', 'value' => 'Cairo, Egypt'],
            ['key' => 'currency', 'value' => 'EGP'],
            ['key' => 'currency_symbol', 'value' => 'E£'],
            ['key' => 'currency_symbol_ar', 'value' => 'ج.م'],
            ['key' => 'delivery_fee', 'value' => '30'],
            ['key' => 'free_shipping_threshold', 'value' => '500'],
            ['key' => 'tax_percentage', 'value' => '14'],
            ['key' => 'commission_percentage', 'value' => '10'],
            ['key' => 'min_withdrawal', 'value' => '100'],
            ['key' => 'max_product_images', 'value' => '10'],
            ['key' => 'otp_expiry_minutes', 'value' => '5'],
            ['key' => 'order_cancel_window_hours', 'value' => '24'],
            ['key' => 'return_window_days', 'value' => '14'],
            ['key' => 'maintenance_mode', 'value' => '0'],
            ['key' => 'registration_enabled', 'value' => '1'],
            ['key' => 'vendor_registration_enabled', 'value' => '1'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}

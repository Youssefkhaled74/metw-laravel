<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class CourierSystemConfigSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Section 3 — automatic procedures (admin-configurable)
            ['key' => 'working_hours_start', 'value' => '10:00'],
            ['key' => 'working_hours_end', 'value' => '22:00'],
            ['key' => 'auto_reject_working_hours', 'value' => '7'],
            ['key' => 'response_window_working_hours', 'value' => '3'],
            ['key' => 'courier_timeout_auto_action', 'value' => 'auto_assign_next'],
            // Max number of couriers offered a single request leg
            ['key' => 'courier_max_offers_per_leg', 'value' => '5'],
            // Sections 3 & 4 — editable failure notification texts
            ['key' => 'courier_failure_message_shipping', 'value' => 'Shipping service is currently unavailable according to the required shipping path'],
            ['key' => 'courier_failure_message_delivery', 'value' => 'Delivery service is currently unavailable according to the required delivery path'],
            // Cron job 3: cancel a User request whose advance stays unpaid for this many real hours
            ['key' => 'cancel_unpaid_advance_hours', 'value' => '12'],
            // Cron job 4: aggregate open sub-shipments every this many days
            ['key' => 'aggregate_sub_shipments_days', 'value' => '3'],
            // Cron job 4: aggregation scope (all | warehouse | group) and its target id
            ['key' => 'aggregate_sub_shipments_scope', 'value' => 'all'],
            ['key' => 'aggregate_sub_shipments_target_id', 'value' => ''],
            // Cron job 5: mark execution start for inter-governorate requests after this many real hours
            ['key' => 'execution_start_hours', 'value' => '24'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}

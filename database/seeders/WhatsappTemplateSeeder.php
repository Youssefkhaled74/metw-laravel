<?php

namespace Database\Seeders;

use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

class WhatsappTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['key' => 'pending', 'content' => 'Your order #{order_number} has been placed and is pending confirmation.', 'is_active' => true],
            ['key' => 'accepted', 'content' => 'Your order #{order_number} has been confirmed and is being prepared.', 'is_active' => true],
            ['key' => 'pickup', 'content' => 'Your order #{order_number} has been picked up by the delivery company.', 'is_active' => true],
            ['key' => 'on_way', 'content' => 'Your order #{order_number} is on its way! Expected delivery: {estimated_date}.', 'is_active' => true],
            ['key' => 'delivered', 'content' => 'Your order #{order_number} has been delivered. Thank you for shopping with us!', 'is_active' => true],
            ['key' => 'cancelled', 'content' => 'Your order #{order_number} has been cancelled. Reason: {reason}.', 'is_active' => true],
        ];

        foreach ($templates as $template) {
            WhatsappTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}

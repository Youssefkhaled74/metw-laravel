<?php

// app/Console/Commands/SendDiscountProductNotifications.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Models\DiscountNotification;
use App\Notifications\OrderStatusUpdated;

class SendDiscountProductNotifications extends Command
{
    protected $signature = 'products:send-discount-notifications';
    protected $description = 'Send notifications for discounted products to users';

    public function handle()
    {
        $now = now();

        $products = Product::where('is_active', true)
            ->where('discount_percentage', '>', 0)
            ->where(function ($q) use ($now) {
                $q->whereNull('discount_start')
                    ->orWhere('discount_start', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('discount_end')
                    ->orWhere('discount_end', '>=', $now);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('product_id')
                      ->from('discount_notifications');
            })
            ->get();

        foreach ($products as $product) {

            // ✅ Chunk users to avoid memory issue
            User::where('notifications_enabled', true)
                ->select(['id', 'fcm_token', 'default_lang'])
                ->chunk(500, function ($users) use ($product) {

                    foreach ($users as $user) {

                        app()->setLocale($user->default_lang ?? 'en');

                        $title = __('notifications.discount_product.title');
                        $body  = __('notifications.discount_product.body', [
                            'product' => $product->name,
                            'discount' => $product->discount_percentage
                        ]);

                        $user->notify(new OrderStatusUpdated(
                            title: $title,
                            body: $body,
                            data: [
                                'key' => 'discount_product',
                                'product_id' => $product->id,
                                'discount' => $product->discount_percentage,
                                'notification_type' => 'ecommerce',
                                'navigation_type' => 'product_sale',
                            ],
                            type: 'ecommerce',
                            navigationType: 'product_sale'
                        ));
                    }
                });

            // ✅ Mark product as notified so it’s sent only once
            DiscountNotification::create([
                'product_id' => $product->id,
                'notified_at' => now(),
            ]);

            $this->info("Notifications sent for product #{$product->id}");
        }

        return Command::SUCCESS;
    }
}

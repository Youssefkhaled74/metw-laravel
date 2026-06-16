<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $users = User::all();

        if ($products->isEmpty() || $users->isEmpty()) return;

        foreach ($products as $product) {
            $reviewCount = rand(1, 3);
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                ProductReview::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'user_id'    => $user->id,
                    ],
                    [
                        'rating'  => rand(3, 5),
                        'comment' => 'Great product! Fast delivery and exactly as described.',
                        'ecommerce_order_item_id' => null,
                    ]
                );
            }
        }
    }
}

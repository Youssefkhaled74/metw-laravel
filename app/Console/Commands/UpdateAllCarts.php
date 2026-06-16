<?php

namespace App\Console\Commands;

use App\Jobs\UpdateCartJob;
use App\Models\EcommerceCart;
use Illuminate\Console\Command;

class UpdateAllCarts extends Command
{
    protected $signature = 'cart:update-all';
    protected $description = 'Update all ecommerce carts and their item prices';

    public function handle(): void
    {
        $this->info('🛒 Starting full cart update...');

        EcommerceCart::chunk(100, function ($carts) {
            foreach ($carts as $cart) {
                dispatch(new UpdateCartJob($cart->id));
            }
        });

        $this->info('✅ All carts queued for update.');
    }
}

<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\EcommerceCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $cartId;
    public function __construct($cartId)
    {
        $this->cartId = $cartId;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cart = EcommerceCart::with('items.product')->find($this->cartId);

        if(!$cart) return;

        $total = 0;
        $itemsCount = 0;

        foreach($cart->items as $item){
            $product = $item->product;

            $unitprice = $product->price;

            $finalPrice = $unitprice;

            $discount = 0;

            if($product->discount_percentage  && $product->is_sale){
                $finalPrice = $product->discounted_price;
                $discount = $unitprice - $finalPrice;
            }
            $totalPrice = $finalPrice * $item->quantity;

            $item->update([
                'unit_price'      => $unitprice,
                'total_price'     => $totalPrice,
                'product_discount'=> $discount,
                'final_price'     => $finalPrice,
            ]);

            $total += $totalPrice;
            $itemsCount += $item->quantity;
        }
        $cart->update([
            'total_price' => $total,
            'items_count' => $itemsCount,
        ]);
    }
}

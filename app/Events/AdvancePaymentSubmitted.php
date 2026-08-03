<?php

namespace App\Events;

use App\Models\AdvancePayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdvancePaymentSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly AdvancePayment $payment) {}
}

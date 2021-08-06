<?php

namespace App\Billing;

use Devinweb\LaravelHyperpay\Contracts\BillingInterface;

class HyperPayBilling implements BillingInterface
{
    /**
     * Get the billing data.
     *
     * @return array
     */
    public function getBillingData(): array
    {
        return [
            //
        ];
    }
}
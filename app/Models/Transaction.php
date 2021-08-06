<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Devinweb\LaravelHyperpay\Models\Transaction as ModelsTransaction;

class Transaction extends ModelsTransaction
{
    use UsesTenantConnection;
}

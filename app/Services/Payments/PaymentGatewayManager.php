<?php

namespace App\Services\Payments;

use App\Services\Payments\Drivers\MpesaDriver;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function __construct(
        protected MpesaDriver $mpesaDriver
    ) {}

    public function driver(string $name = 'mpesa')
    {
        return match (strtolower($name)) {
            'mpesa' => $this->mpesaDriver,
            default => throw new InvalidArgumentException("Driver de pagamento [{$name}] não suportado."),
        };
    }
}

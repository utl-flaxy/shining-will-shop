<?php

namespace App\Services;

use Square\SquareClient;

class SquarePaymentService
{
    public static function client(): SquareClient
    {
        return new SquareClient([
            'accessToken' => config('services.square.access_token'),
            'environment' => config('services.square.environment'),
        ]);
    }
}

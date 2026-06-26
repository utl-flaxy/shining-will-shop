<?php

namespace App\Services;

use Square\Environment;
use Square\SquareClient;
use Square\Models\Money;
use Square\Models\CreatePaymentRequest;
use Square\Models\CreatePaymentResponse;
use Square\Models\RefundPaymentRequest;
use Illuminate\Support\Str;

class SquareService
{
    protected SquareClient $client;

    public function __construct()
    {
        $this->client = new SquareClient([
            'accessToken' => config('services.square.access_token'),
            'environment' => config('services.square.env') === 'sandbox'
                ? Environment::SANDBOX
                : Environment::PRODUCTION,
        ]);
    }

    /**
     * ✅ 決済作成
     */
    public function createPayment(int $amount, string $sourceId, array $metadata = [])
    {
        $money = new Money();
        $money->setAmount($amount); // 円 × 100
        $money->setCurrency('JPY');

        $request = new CreatePaymentRequest(
            $sourceId,
            Str::uuid(),
            $money
        );

        $request->setLocationId(config('services.square.location_id'));
        $request->setNote('Shining Will Shop 決済');
        $request->setMetadata($metadata);

        $response = $this->client
            ->getPaymentsApi()
            ->createPayment($request);

        if ($response->isError()) {
            throw new \Exception(json_encode($response->getErrors()));
        }

        return $response->getResult();
    }

    /**
     * ✅ 決済情報取得
     */
    public function getPayment(string $paymentId)
    {
        $response = $this->client
            ->getPaymentsApi()
            ->getPayment($paymentId);

        if ($response->isError()) {
            throw new \Exception(json_encode($response->getErrors()));
        }

        return $response->getResult()->getPayment();
    }

    /**
     * ✅ 返金処理（Square用）
     */
    public function refund(string $paymentId, int $amount)
    {
        $money = new Money();
        $money->setAmount($amount);
        $money->setCurrency('JPY');

        $request = new RefundPaymentRequest(
            Str::uuid(),
            $paymentId,
            $money
        );

        $response = $this->client
            ->getRefundsApi()
            ->refundPayment($request);

        if ($response->isError()) {
            throw new \Exception(json_encode($response->getErrors()));
        }

        return $response->getResult();
    }
}

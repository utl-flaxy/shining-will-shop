<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;

class RefundController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function refund($payment_intent)
    {
        try {
            $refund = $this->stripe->refund($payment_intent);
            return back()->with('success', "返金が完了しました（ID: {$refund->id}）");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

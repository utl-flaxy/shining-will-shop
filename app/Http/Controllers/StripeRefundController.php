<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;

class StripeRefundController extends Controller
{
    public function refund($id, StripeService $stripe)
    {
        try {
            $stripe->refund($id);
            return back()->with('success', '返金が完了しました。');
        } catch (\Exception $e) {
            return back()->with('error', '返金に失敗しました: ' . $e->getMessage());
        }
    }
}

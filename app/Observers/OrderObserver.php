<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        /*
        |--------------------------------------------------------------------------
        | ステータス変更以外は何もしない
        |--------------------------------------------------------------------------
        */

        if (! $order->wasChanged('status')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 発送済み以外は何もしない
        |--------------------------------------------------------------------------
        */

        if ($order->status !== OrderStatus::Shipped) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | メールアドレスが無ければ送信しない
        |--------------------------------------------------------------------------
        */

        if (blank($order->customer_email)) {

            Log::warning(
                '発送メール送信スキップ（メールアドレスなし）',
                [
                    'order_id' => $order->id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 注文明細を読み込む
        |--------------------------------------------------------------------------
        */

        $order->loadMissing([
            'items.product',
            'items.variant',
            'user',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 発送メール送信
        |--------------------------------------------------------------------------
        */

        Mail::to($order->customer_email)
            ->send(new OrderShippedMail($order));

        /*
        |--------------------------------------------------------------------------
        | ログ
        |--------------------------------------------------------------------------
        */

        Log::info(
            '発送メール送信完了',
            [
                'order_id' => $order->id,
                'email' => $order->customer_email,
            ]
        );
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}

<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRefundedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function build()
    {
        return $this
            ->subject('【Shining Will Shop】返金処理完了のお知らせ')
            ->markdown('emails.orders.refunded', [
                'order' => $this->order,
            ]);
    }
}

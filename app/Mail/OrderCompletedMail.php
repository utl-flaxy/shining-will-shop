<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public bool $isAdmin;

    public function __construct(Order $order, bool $isAdmin = false)
    {
        $this->order = $order;
        $this->isAdmin = $isAdmin;
    }

    public function build()
    {
        return $this
            ->subject(
                $this->isAdmin
                    ? '【管理者通知】新しい注文が入りました'
                    : '【Shining Will Shop】ご注文ありがとうございます'
            )
            ->view('emails.order_completed');
    }
}

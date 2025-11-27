<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'subtotal',
        'shipping_fee',
        'total_amount',
        'status',
        'tracking_number',
        'shipped_at',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
    ];

    // 関連（注文→注文商品）
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ステータスの日本語表示
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => '入金待ち',
            'paid' => '入金確認',
            'shipped' => '発送済み',
            'refunded' => '返金済み',
            default => '不明',
        };
    }
}

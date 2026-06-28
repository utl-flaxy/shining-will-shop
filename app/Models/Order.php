<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        'order_number',

        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',

        'delivery_method',

        'shipping_company',

        'subtotal',
        'shipping_fee',
        'total_amount',

        'status',

        'tracking_number',
        'shipped_at',

        'payment_method',
        'payment_status',
        'paid_at',
        'bank_deposit_confirmed_at',

        'note_to_talent',

        'refunded_amount',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'status'                    => OrderStatus::class,

        'shipped_at'                => 'datetime',
        'paid_at'                   => 'datetime',
        'bank_deposit_confirmed_at' => 'datetime',
        'refunded_at'               => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */

    /**
     * 注文ユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 注文明細
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * エイリアス
     */
    public function orderItems()
    {
        return $this->items();
    }

    /*
    |--------------------------------------------------------------------------
    | 在庫減算
    |--------------------------------------------------------------------------
    */

    public function decreaseStock(): void
    {
        $this->loadMissing([
            'items.variant',
            'items.product',
        ]);

        foreach ($this->items as $item) {

            $qty = (int) $item->quantity;

            if ($qty <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | バリアント在庫
            |--------------------------------------------------------------------------
            */

            if ($item->variant) {

                $variant = ProductVariant::query()
                    ->lockForUpdate()
                    ->find($item->variant->id);

                if (! $variant) {
                    throw new \RuntimeException(
                        '商品バリアントが存在しません。'
                    );
                }

                if ($variant->stock < $qty) {
                    throw new \RuntimeException(
                        "{$item->product_name} の在庫が不足しています。"
                    );
                }

                $variant->decrement('stock', $qty);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | 通常商品
            |--------------------------------------------------------------------------
            */

            if (
                $item->product &&
                $item->product->is_stock_managed
            ) {

                $product = Product::query()
                    ->lockForUpdate()
                    ->find($item->product->id);

                if (! $product) {
                    continue;
                }

                $product->decrement('stock', $qty);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    /**
     * 注文ステータス表示
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status instanceof OrderStatus) {
            return $this->status->label();
        }

        return OrderStatus::from($this->status)->label();
    }

    /**
     * 支払い方法
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {

            'card' => 'クレジットカード',

            'bank_transfer' => '口座振込',

            'on_site' => '現地払い',

            default => $this->payment_method,

        };
    }

    /**
     * 決済状況
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {

            'unpaid' => '未入金',

            'paid' => '入金済み',

            'refunded' => '返金済み',

            'failed' => '決済エラー',

            default => $this->payment_status,

        };
    }

    /**
     * 配送会社
     */
    public function getShippingCompanyLabelAttribute(): string
    {
        return match ($this->shipping_company) {

        'sagawa' => '佐川急便',

        'yamato' => 'ヤマト運輸',

        'japan_post' => '日本郵便',

        default => '-',

        };
    }

    /**
     * 配送追跡URL
     */
    public function getTrackingUrlAttribute(): ?string
    {
        if (
            empty($this->tracking_number) ||
	    empty($this->shipping_company)
	) {
	   return null;
	}

	return match ($this->shipping_company) {

	    'sagawa'
	        => 'https://k2k.sagawa-exp.co.jp/p/web/okurijoinput.jsp'
	            . '?okurino='
	            . urlencode($this->tracking_number),

	    'yamato'
	        => 'https://toi.kuronekoyamato.co.jp/cgi-bin/tneko'
	            . '?number='
	            . urlencode($this->tracking_number),

	    'japan_post'
	        => 'https://trackings.post.japanpost.jp/services/srv/search/direct'
	            . '?reqCodeNo1='
	            . urlencode($this->tracking_number),

	    default => null,
	};
    }

}

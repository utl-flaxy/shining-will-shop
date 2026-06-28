<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductVariant;

class Order extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | 注文ステータス
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => '受付',
            self::STATUS_PREPARING => '発送準備中',
            self::STATUS_SHIPPED => '発送済み',
            self::STATUS_COMPLETED => '配送完了',
            self::STATUS_CANCELLED => 'キャンセル',
        ];
    }

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',

        'delivery_method',

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

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

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
            | メンバー別在庫
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

                $variant->decrement(
                    'stock',
                    $qty
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | 通常商品の在庫
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

                $product->decrement(
                    'stock',
                    $qty
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? '不明';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {

            'card' => 'クレジットカード',

            'bank_transfer' => '口座振込',

            'on_site' => '現地払い',

            default => $this->payment_method,

        };
    }

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

    public function getDeliveryMethodLabelAttribute(): string
    {
        return match ($this->delivery_method) {

            'sagawa' => '佐川配送',

            'pickup' => '現地渡し',

            default => $this->delivery_method,

        };
    }
}

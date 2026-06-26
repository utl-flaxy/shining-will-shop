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

        'delivery_method',

        'subtotal',
        'shipping_fee',
        'total_amount',

        'status', // pending / paid / shipped / refunded

        'tracking_number',
        'shipped_at',

        'payment_method',   // card / bank_transfer / on_site
        'payment_status',   // unpaid / paid / refunded / failed
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

    /* ============================
        ✅ リレーション
    ============================ */

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // orderItems という名前でも同じもの（CSVなど互換用）
    public function orderItems()
    {
        return $this->items();
    }

    /* ============================
        ✅ 在庫連動メソッド
    ============================ */

    /**
     * この注文分だけ在庫を減らす
     *
     * - product_variant_id がある場合 → バリアント在庫を優先して減算
     * - なければ manage_stock=true の Product の stock を減算
     */
    public function decreaseStock(): void
    {
        $this->loadMissing(['items.variant', 'items.product']);

        foreach ($this->items as $item) {
            $qty = (int) $item->quantity;
            if ($qty <= 0) {
                continue;
            }

            // ① バリアント在庫（メンバー別）を減らす
            if ($item->variant) {
                $variant  = $item->variant;
                $newStock = max(0, (int) $variant->stock - $qty);

                $variant->update(['stock' => $newStock]);
                continue;
            }

            // ② バリアントが無い場合は商品在庫を減らす
            if ($item->product && $item->product->manage_stock) {
                $product  = $item->product;
                $newStock = max(0, (int) $product->stock - $qty);

                $product->update(['stock' => $newStock]);
            }
        }
    }

    /**
     * キャンセルや全額返金で在庫を戻したいとき用（将来拡張）
     */
    public function restoreStock(): void
    {
        $this->loadMissing(['items.variant', 'items.product']);

        foreach ($this->items as $item) {
            $qty = (int) $item->quantity;
            if ($qty <= 0) {
                continue;
            }

            if ($item->variant) {
                $item->variant->increment('stock', $qty);
                continue;
            }

            if ($item->product && $item->product->manage_stock) {
                $item->product->increment('stock', $qty);
            }
        }
    }

    /* ============================
        ✅ 日本語ラベル系
    ============================ */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '入金待ち',
            'paid'     => '入金確認',
            'shipped'  => '発送済み',
            'refunded' => '返金済み',
            default    => '不明',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'card'          => 'クレジットカード',
            'bank_transfer' => '口座振込',
            'on_site'       => '現地払い',
            default         => $this->payment_method,
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid'   => '未入金',
            'paid'     => '入金済み',
            'refunded' => '返金済み',
            'failed'   => '決済エラー',
            default    => $this->payment_status,
        };
    }

    public function getDeliveryMethodLabelAttribute(): string
    {
        return match ($this->delivery_method) {
            'sagawa' => '佐川配送',
            'pickup' => '現場渡し',
            default  => $this->delivery_method,
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'title',
        'sku',
        'price',
        'qty',
        'subtotal',
        'meta',
    ];

    protected $casts = [
        'price' => 'integer',
        'qty' => 'integer',
        'subtotal' => 'integer',
        'meta' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

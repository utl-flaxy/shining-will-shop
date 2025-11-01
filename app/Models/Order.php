<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'subtotal',
        'shipping_fee',
        'tax_amount',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'shipping_address',
        'meta',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'meta' => 'array',
        'subtotal' => 'integer',
        'shipping_fee' => 'integer',
        'tax_amount' => 'integer',
        'total_amount' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model') ?? \App\Models\User::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}

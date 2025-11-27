<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'description',
        'is_published',
        'category_id',
        'starts_at',
        'ends_at',
        'manage_stock',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_published' => 'boolean',
        'manage_stock' => 'boolean',
    ];

    // ==============================
    // 🔗 リレーション
    // ==============================
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ==============================
    // 📦 スコープ
    // ==============================
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeActive($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    // ==============================
    // ⚙️ 補助メソッド
    // ==============================
    public function isAvailableForSale(): bool
    {
        if (!$this->is_published) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->ends_at && $this->ends_at->isPast()) return false;
        return true;
    }

    public function totalStock(): int
    {
        return $this->manage_stock
            ? $this->variants->sum('stock')
            : 9999; // 在庫管理しない場合は無制限扱い
    }
}

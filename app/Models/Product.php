<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'sku',
        'is_published',
        'is_active',
        // マイグレーションに合わせる
        'is_stock_managed',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function isSoldOut()
    {
        return $this->totalStock() <= 0;
    }

    public function totalStock()
    {
        // migration のカラム名に合わせて判定
        if (! $this->is_stock_managed) {
            return 9999;
        }

        if ($this->variants()->count() > 0) {
            return $this->variants()->sum('stock');
        }

        return $this->stock;
    }

    public function isAvailableForSale(): bool
    {
        if (isset($this->is_active) && ! $this->is_active) {
            return false;
        }

        if (isset($this->is_published) && ! $this->is_published) {
            return false;
        }

        if ($this->is_stock_managed && $this->isSoldOut()) {
            return false;
        }

        return true;
    }
}

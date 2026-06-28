<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'is_stock_managed',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | 販売状態
    |--------------------------------------------------------------------------
    */

    public function isSoldOut(): bool
    {
        return $this->totalStock() <= 0;
    }

    public function totalStock(): int
    {
        /*
        |--------------------------------------------------------------------------
        | 在庫管理しない商品
        |--------------------------------------------------------------------------
        */

        if (! $this->is_stock_managed) {
            return 999999;
        }

        /*
        |--------------------------------------------------------------------------
        | メンバー別在庫
        |--------------------------------------------------------------------------
        */

        if ($this->variants()->exists()) {
            return (int) $this->variants()->sum('stock');
        }

        /*
        |--------------------------------------------------------------------------
        | 通常在庫
        |--------------------------------------------------------------------------
        */

        return (int) $this->stock;
    }

    public function isAvailableForSale(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! $this->is_published) {
            return false;
        }

        if ($this->is_stock_managed && $this->isSoldOut()) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * 公開中のみ
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('is_published', true);
    }

    /**
     * キーワード検索
     */
    public function scopeKeyword(
        Builder $query,
        ?string $keyword
    ): Builder {

        if (blank($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {

            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");

        });
    }

    /**
     * カテゴリ検索
     */
    public function scopeCategory(
        Builder $query,
        $categoryId
    ): Builder {

        if (blank($categoryId)) {
            return $query;
        }

        return $query->where(
            'category_id',
            $categoryId
        );
    }

    /**
     * 並び替え
     */
    public function scopeSort(
        Builder $query,
        ?string $sort
    ): Builder {

        return match ($sort) {

            'price_asc'
                => $query->orderBy('price'),

            'price_desc'
                => $query->orderByDesc('price'),

            'name'
                => $query->orderBy('name'),

            default
                => $query->latest(),

        };
    }
}

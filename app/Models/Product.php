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

    protected $appends = [
        'main_image_url',
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
    | アクセサ
    |--------------------------------------------------------------------------
    */

    public function getMainImageUrlAttribute(): string
    {
        $image = $this->images()
            ->orderBy('sort_order')
            ->first();

        if (! $image) {
            return asset('images/no-image.png');
        }

        return asset('storage/' . ltrim($image->url, '/'));
    }

    /*
    |--------------------------------------------------------------------------
    | 在庫
    |--------------------------------------------------------------------------
    */

    public function isSoldOut(): bool
    {
        return $this->totalStock() <= 0;
    }

    public function totalStock(): int
    {
        if (! $this->is_stock_managed) {
            return 999999;
        }

        if ($this->variants()->exists()) {
            return (int) $this->variants()->sum('stock');
        }

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

        if (
            $this->is_stock_managed &&
            $this->isSoldOut()
        ) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

    /**
     * 公開商品
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

        return $query->where(function (Builder $query) use ($keyword) {

            $query

                ->where('name', 'like', "%{$keyword}%")

                ->orWhere('description', 'like', "%{$keyword}%")

                ->orWhere('sku', 'like', "%{$keyword}%")

                ->orWhereHas('category', function (Builder $query) use ($keyword) {

                    $query->where(
                        'name',
                        'like',
                        "%{$keyword}%"
                    );

                });

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

            'oldest'
                => $query->oldest(),

            default
                => $query->latest(),

        };
    }
}

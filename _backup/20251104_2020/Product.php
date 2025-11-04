<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $guarded = [];
    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Product belongs to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope: Search by keyword (name or description)
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory(Builder $query, $categoryId): Builder
    {
        if (empty($categoryId)) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Filter by price range
     */
    public function scopePriceRange(Builder $query, ?int $minPrice, ?int $maxPrice): Builder
    {
        if ($minPrice !== null && $minPrice >= 0) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null && $maxPrice >= 0) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    /**
     * Scope: Only active products
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

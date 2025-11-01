<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'sku',
        'stock',
        'images',
        'is_published',
        'image',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'is_published' => 'boolean',
        'price' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function firstImage(): ?string
    {
        $images = $this->images ?? [];
        if (is_array($images) && count($images)) {
            return $images[0];
        }
        return $this->image ?? null;
    }

    /**
     * 互換アクセス：既存コードで $product->name を参照している箇所に対応するため
     * title があればそれを返す（後で安全に name 参照を置換できます）。
     */
    public function getNameAttribute(): ?string
    {
        return $this->attributes['title'] ?? null;
    }
}

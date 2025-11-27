<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id','member_name','sku','stock','alert_threshold',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

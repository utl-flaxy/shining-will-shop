<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryReservation extends Model
{
    protected $guarded = [];

    protected $dates = ['expires_at'];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->lt(Carbon::now());
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
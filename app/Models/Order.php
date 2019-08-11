<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';
    const STATUS_COMPLETE = 'complete';

    const CURRENCY_BDT = 'BDT';

    protected $fillable = [
        'user_id',
        'total_price',
        'coupon_id',
        'coupon_amount',
        'paid_amount',
        'status',
        'transaction_id',
        'currency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', // null for guests
        'email', // required for guests
        'total_amount',
        'currency_code',
        'status', // pending, paid, failed
        'payment_provider', // paypal, cinetpay, paydunia
        'payment_id',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

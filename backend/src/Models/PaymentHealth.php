<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHealth extends Model
{
    protected $table = 'payment_health';
    
    protected $fillable = [
        'provider', // paypal, cinetpay, paydunia
        'status', // UP, DOWN, DEGRADED
        'latency_ms',
        'last_check_at',
        'error_message',
    ];

    public $timestamps = false;
}

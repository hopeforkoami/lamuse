<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entitlement extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'song_id',
        'order_id',
        'access_token',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

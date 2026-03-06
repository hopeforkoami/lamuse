<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'song_id',
        'price',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
